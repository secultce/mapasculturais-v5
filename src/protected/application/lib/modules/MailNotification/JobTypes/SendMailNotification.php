<?php

namespace MailNotification\JobTypes;

use MapasCulturais\App;
use MapasCulturais\Definitions\JobType;
use MapasCulturais\Entities\Job;
use MapasCulturais\Services\AmqpQueueService;
use MapasCulturais\Services\SentryService;

class SendMailNotification extends JobType
{
    const SLUG = "sendmailnotification";

    protected function _generateId(array $data, string $start_string, string $interval_string, int $iterations)
    {
        return "sendmailnotification:{$data['registrationId']}".uniqid();
    }

    protected function _execute(Job $job)
    {

        $app = App::i();

        $registration = $app->repo("Registration")->find($job->registrationId);
       
        $message = $app->renderMailerTemplate($job->template, [
            'siteName' => $app->view->dict('site: name', false),
            'baseUrl' => $job->baseUrl ?: $app->getBaseUrl(),
            'userName' => $registration->owner->name,
            'projectId' => $registration->opportunity->id,
            'projectName' => $registration->opportunity->name,
            'registrationId' => $registration->id,
            'registrationNumber' => $registration->number,
            'statusTitle' => $registration->getStatusNameById($registration->status),
            'statusNum' => $registration->status,
        ]);
      
        $email_params = [
            'from' => $app->config['mailer.from'],
            'to' => $registration->owner->emailPrivado ?: $registration->owner->user->email,
            'subject' => $message['title'],
            'body' => $message['body'],
        ];

        if (env('MAIL_NOTIFICATION_TRANSPORT', 'rabbitmq') === 'smtp') {
            return $app->createAndSendMailMessage($email_params);
        }

        try {
            $queueService = new AmqpQueueService();
            $queueService->sendMessage(
                $app->config['rabbitmq']['exchange_default'],
                $app->config['rabbitmq']['routing']['module_mail_notification'],
                [
                    'message_id' => $job->id,
                    'event' => $job->template === 'start_registration' ? 'registration.start' : 'registration.send',
                    'template' => $job->template,
                    'locale' => $app->config['app.lcode'],
                    'registration_id' => $registration->id,
                    'to' => $email_params['to'],
                    'from' => $email_params['from'],
                    'subject' => $email_params['subject'],
                    'body' => $email_params['body'],
                    'created_at' => date('c'),
                ],
                $app->config['rabbitmq']['queues']['queue_mail_notification']
            );
        } catch (\Throwable $e) {
            $this->scheduleRetry($job, $e);
            return false;
        }

        return true;
    }

    /**
     * O executor de jobs não reprocessa jobs com erro (ficam com status 1 para sempre).
     * Então, em caso de falha na publicação, o próprio job volta para a fila.
     */
    protected function scheduleRetry(Job $job, \Throwable $e)
    {
        $app = App::i();

        $attempts = (int) $job->publishAttempts + 1;
        $max_attempts = (int) env('MAIL_NOTIFICATION_MAX_ATTEMPTS', 5);

        if ($attempts >= $max_attempts) {
            $app->log->error("Job {$job->id}: falha ao publicar na fila após {$attempts} tentativas, desistindo: {$e->getMessage()}");
            SentryService::captureExceptions($e);
            return;
        }

        $app->log->warning("Job {$job->id}: falha ao publicar na fila (tentativa {$attempts} de {$max_attempts}), nova tentativa em 5 minutos: {$e->getMessage()}");

        $job->publishAttempts = $attempts;
        $job->status = Job::STATUS_WAITING;
        $job->nextExecutionTimestamp = new \DateTime('+5 minutes');
        $job->save(true);
    }
}
