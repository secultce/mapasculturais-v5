<?php

namespace Diligence\Controllers;

use Carbon\Carbon;
use Diligence\Entities\AnswerDiligence;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Entities\NotificationDiligence;
use Diligence\Repositories\Diligence as DiligenceRepo;
use Diligence\Service\NotificationInterface;
use MapasCulturais\App;
use MapasCulturais\Entity;
use MapasCulturais\Entities\RegistrationMeta;

class Controller extends \MapasCulturais\Controller implements NotificationInterface
{
    use \Diligence\Traits\DiligenceSingle;
    use \MapasCulturais\Traits\ControllerUploads;

    const WITHOUT_DILIGENCE = 'sem_diligencia';
    const DILIGENCE_OPEN    = 'diligencia_aberta';
    const ANSWER_DRAFT      = 'resposta_rascunho';
    const ANSWER_SEND       = 'resposta_enviada';


    /**
     * Salva uma diligência
     *
     * @return void
     */
    public function POST_save(): void
    {
        $this->requireAuthentication();

        $app = App::i();
        $registration = $app->repo('Registration')->find($this->data['registration']);
        // Consulta se tem diligencia
        $isDiligence = $app->repo('Diligence\Entities\Diligence')->findOneBy(['registration' => $this->data['registration']]);
        // Se não tiver diligencia ou se quem abriu a diligencia é a mesma pessoa logada poderá alterar o registro
        if(is_null($isDiligence) || $isDiligence->openAgent->id == $app->user->profile->id){
            if (($this->data['idDiligence'] ?: 0) == 0 && (is_null($registration->opportunity->use_multiple_diligence) || $registration->opportunity->use_multiple_diligence === 'Não')) {
                $diligences = $app->repo(EntityDiligence::class)->findBy([
                    'registration' => $registration,
                    'status' => [EntityDiligence::STATUS_DRAFT, EntityDiligence::STATUS_OPEN, EntityDiligence::STATUS_SEND]
                ]);

                if (count($diligences) > 0) {
                    $this->json([
                        'message' => 'Já foi aberta uma diligência para essa inscrição. Não é permitida a abertura de outra',
                        'error' => 'multiple_diligence_not_alowed',
                    ], 400);

                    return;
                }
            }

            $answer = new EntityDiligence();
            $entity = $answer->createOrUpdate($this);

            $this->json(['message' => 'success', 'status' => 200, 'entityId' => $entity['entityId']]);
        }else{
            $this->json(['message' => 'Essa prestação de conta já está em diligência.', 'status' => 403]);
        }

    }

    /**
     * Busca o conteúdo de uma diligência salva ou enviada
     *
     * @return void
     */
    public function GET_getcontent(): void
    {
        $app = App::i();

        // ID é o número da inscrição
        if (isset($this->data['id'])) {
            // Repositorio da Diligencia
            $diligences = $app->repo('Diligence\Entities\Diligence')
                ->findBy(
                    ['registration' => $this->data['id']],
                    ['createTimestamp' => 'desc']
                );

            $message = self::WITHOUT_DILIGENCE;

            if (count($diligences) > 0) {
                $lastDiligence = $diligences[0];

                if (in_array($lastDiligence->status, [EntityDiligence::STATUS_OPEN, EntityDiligence::STATUS_SEND])) {
                    $message = self::DILIGENCE_OPEN;
                }

                if (!is_null($lastDiligence->answer)) {
                    if ($lastDiligence->answer->status === AnswerDiligence::STATUS_OPEN) {
                        $message = self::ANSWER_DRAFT;
                    }
                    if ($lastDiligence->answer->status === AnswerDiligence::STATUS_SEND) {
                        $message = self::ANSWER_SEND;
                    }
                }
            }

            $this->json(['message' => $message, 'data' => $diligences]);
        }

        //Passando para o hook o conteúdo da instância diligencia
        $app->applyHook('controller(diligence).getContent', [&$diligences]);
        //Validação caso nao tenha a inscrição na URL
        $this->json(['message' => 'Falta a inscrição', 'status' => 'error'], 400);
    }

    /**
     * Metodo da interface para notificação
     *
     * @return void
     */
    public function notification(): void
    {
        $this->requireAuthentication();
        App::i()->applyHook('controller(diligence).notification:before');
        //Notificação no Mapa Cultural
        $notification = new NotificationDiligence();
        $notification->create($this, EntityDiligence::TYPE_NOTIFICATION_AUDITOR);

        $userDestination = $notification->userDestination($this);
        App::i()->applyHook('controller(diligence).notification:after');
        //Enviando para fila RabbitMQ
        EntityDiligence::sendQueue($userDestination, 'proponente');
        self::returnJson(null, $this);
    }

    public function POST_sendNotification()
    {
        self::notification();
    }
    /**
     * Rsposta do proponente
     *
     * @return void
     */
    public function POST_answer(): void
    {
        if ($this->data['answer'] == '') {
            $this->errorJson(['message' => 'O Campo de resposta deve está preenchido'], 400);
        }
        $this->requireAuthentication();
        $answer = new AnswerDiligence();
        $entity = $answer->createOrUpdate($this);
        $return = json_decode($entity);
        $this->json(['message' => 'success', 'status' => 200, 'entityId' => $return->entityId]);
    }

    /**
     * Altera o status da diligência, retornando para rascunho
     *
     * @return void
     */
    public function PUT_cancelsend(): void
    {
        $this->requireAuthentication();
        $cancel = new EntityDiligence();
        $cancel->cancel($this);
    }

    public function POST_notifiAnswer()
    {
        // Cria a notificação dentro do painel
        $notification = new NotificationDiligence();
        $notification->create($this, EntityDiligence::TYPE_NOTIFICATION_PROPONENT);

        // Adiciona notificação por e-mail à fila
        $app = App::i();
        $dili = $app->repo('\Diligence\Entities\Diligence')->findBy(['registration' => $this->data['registration']]);
        $userDestination = [];
        foreach ($dili as $diligence) {
            $userDestination = [
                'registration' => $this->data['registration'],
                'comission' => $diligence->openAgent->user->email,
                'owner' => $diligence->registration->opportunity->owner->user->email
            ];
        };
        EntityDiligence::sendQueue($userDestination, 'resposta');
        $this->json(['message' => 'success', 'status' => 200]);
    }

    /**
     * Altera o status da resposta, retornando para rascunho
     *
     * @return void
     */
    public function PUT_cancelsendAnswer()
    {
        $this->requireAuthentication();
        $cancel = new AnswerDiligence();
        $cancel->cancel($this);
    }

    public function POST_valueProject(): void
    {
        $this->requireAuthentication();
        $app = App::i();

        $regMeta = [];
        $idEntity = $this->postData['entity'];
        $reg = $app->repo('Registration')->find($idEntity);
        $createMetadata = null;
        $regMeta = $app->repo('RegistrationMeta')->findBy([
            'owner' => $idEntity
        ]);
        foreach ($this->postData as $keyRequest => $meta) {

            if (empty($regMeta)) {
                $createMeta = self::authorizedProject($reg, $keyRequest, $meta);
                self::saveEntity($createMeta);
            }

            foreach ($regMeta as $key => $value) {
                //Se já existe dados cadastrados, então substitui por um valor novo
                if ($value->key == $keyRequest) {
                    $value->value = $meta;
                    self::saveEntity($value);
                }
            }

            $createMetadata = $app->repo('RegistrationMeta')->findBy([
                'key' => $keyRequest, 'owner' => $idEntity
            ]);

            if (empty($createMetadata)) {
                $createMeta = self::authorizedProject($reg, $keyRequest, $meta);
                self::saveEntity($createMeta);
            }
        }
        self::returnJson(null, $this);
    }

    protected function authorizedProject($entity, $key, $value): object
    {
        $metaData = new RegistrationMeta();
        $metaData->key = $key;
        $metaData->value = $value;
        $metaData->owner = $entity;
        return $metaData;
    }

    public function GET_getAuthorizedProject()
    {
        $authorized = DiligenceRepo::getAuthorizedProject($this->data['id']);
        $this->json([
            'optionAuthorized' => $authorized['optionAuthorized'],
            'valueAuthorized' => $authorized['valueAuthorized']
        ]);
    }

    /**
     * Excluir arquivos da diligência
     *
     * @return boolean
     */
    public function GET_deleteFile(): void
    {
        $app = App::i();
        $conn = $app->em->getConnection();

        $registrationId = $this->data[1];
        $file = $app->repo('File')->findBy(['id' => $this->data['id']])[0];

        //Verificando se existe na rota esse indice
        if (isset($registrationId)) {
            $entity = $app->repo('Registration')->find($registrationId);
            //Verificando se o dono da inscrição é o mesmo usuario logado
            if ($entity->getOwnerUser() == $app->getUser()) {
                $stmt = $conn->prepare('DELETE FROM file WHERE id = :id');
                $stmt->bindParam('id', $this->data['id']);
                $affectedRows = $stmt->executeStatement();
                if ($affectedRows) {
                    unlink($file->path);
                    self::returnJson(null, $this);
                }
            }
        }
        $this->errorJson(['message' => 'Erro Inexperado', 'status' => 400], 400);
    }

    function addingBusinessDays($date, $dias): Carbon
    {
        // Obtém a data e hora atual em objeto tyipo date
        $currentDate = Carbon::parse($date);
        $daysAdds = 0;
        //Consultando no banco os feriados nacionais cadastrados
        $app = App::i();
        $termsHolidays = $app->repo('Term')->findBy(['taxonomy' => 'holiday']);
        $holidays = array_map(function ($term) {
            return $term->term;
        }, $termsHolidays);

        // Loop até que todos os dias úteis sejam adicionados
        while ($daysAdds < $dias) {
            // Adiciona 1 dia à data atual
            $currentDate->addDay();

            // Verifica se o dia adicionado é um dia útil (segunda a sexta-feira)
            if ($currentDate->isWeekday()) {
                //verificando se a data está no array dos feriados, se tiver nao realiza o incremento
                $holiday = $currentDate->format('m-d');
                if (!in_array($holiday, $holidays)) {
                    $daysAdds++;
                }
            }
        }

        return $currentDate;
    }

    function POST_upload(): void
    {
        $owner = DiligenceRepo::findBy('Diligence\Entities\Diligence', ['id' => $this->data["id"]])[0];
        $savedFiles = DiligenceRepo::getFilesDiligence($this->data["id"]);
        $useMultiDiligence = $owner->registration->opportunity->use_multiple_diligence;

        if (count($savedFiles) >= 2 && (is_null($useMultiDiligence) || $useMultiDiligence == 'Não')) return;

        $this->requireAuthentication();

        if (!$owner) {
            $this->errorJson(\MapasCulturais\i::__('O dono não existe'));
            return;
        }

        $file_class_name = 'Diligence\Entities\DiligenceFile';

        $app = App::i();

        if (empty($_FILES) || !$this->data['id']) {
            $this->errorJson(\MapasCulturais\i::__('Nenhum arquivo enviado'));
            return;
        }

        $result = [];
        $files = [];

        foreach (array_keys($_FILES) as $group_name) {
            $ext = pathinfo($_FILES[$group_name]['name'], PATHINFO_EXTENSION);
            $name = pathinfo($_FILES[$group_name]['name'], PATHINFO_FILENAME);
            $_FILES[$group_name]['name'] = $app->slugify($name) . "." . $ext;

            $upload_group = $app->getRegisteredFileGroup($this->id, $group_name);

            if ($upload_group = $app->getRegisteredFileGroup($this->id, $group_name)) {
                try {
                    $file = $app->handleUpload($group_name, $file_class_name);

                    if (is_array($file) && $upload_group->unique) {
                        continue;
                    } elseif (is_array($file) && !$upload_group->unique) {
                        foreach ($file as $f) {
                            if ($error = $upload_group->getError($f)) {
                                $files[] = ['error' => $error, 'group' => $upload_group];
                            } else {
                                $f->group = $group_name;
                                $files[] = $f;
                            }
                        }
                    } else {
                        if (key_exists('description', $this->data) && is_array($this->data['description']) && key_exists($group_name, $this->data['description']))
                            $file->description = $this->data['description'][$group_name];

                        if ($errors = $file->getValidationErrors()) {
                            $error_messages = [];
                            foreach ($errors as $_errors) {
                                $error_messages = array_merge(array_values($_errors), $error_messages);
                            }
                            $files[$group_name] = ['error' => implode(', ', $error_messages), 'group' => $upload_group];
                        } else if ($error = $upload_group->getError($file)) {
                            $files[] = ['error' => $error, 'group' => $upload_group];
                        } else {
                            $file->group = $group_name;
                            $files[] = $file;
                        }
                    }
                } catch (\MapasCulturais\Exceptions\FileUploadError $e) {

                    $files[] = [
                        'error' => $e->message,
                        'group' => $upload_group
                    ];
                }
            }
        }

        if (empty($files)) {
            $this->errorJson(\MapasCulturais\i::__('Nenhum arquivo válido enviado'));
            return;
        } else {
            $all_files_contains_error = true;
            foreach ($files as $f) {
                if (is_object($f)) {
                    $all_files_contains_error = false;
                    break;
                }
            }

            if ($all_files_contains_error) {
                $result = [];
                foreach ($files as $error)
                    if (key_exists('group', $error) && $error['group']->unique)
                        $result[$error['group']->name] = $error['error'];
                    else {
                        if (!isset($result[$error['group']->name])) {
                            $result[$error['group']->name] = [];
                        }
                        $result[$error['group']->name][] = $error['error'];
                    }
                $this->errorJson($result);
                return;
            }
        }

        foreach ($files as $file) {
            $upload_group = $app->getRegisteredFileGroup($this->id, $file->group);

            $file->owner = $owner;
            $file->private = true;

            if ($upload_group->unique) {
                $old_file = $app->repo($file_class_name)->findOneBy(['owner' => $owner, 'group' => $file->group]);
                if ($old_file)
                    $old_file->delete();
            }

            $file->save();
            $file_group = $file->group;

            if ($upload_group->unique) {
                $result[$file_group] = $file;
            } else {
                if (!key_exists($file->group, $result))
                    $result[$file->group] = [];
                $result[$file_group][] = $file;
            }
        }

        $app->em->flush();
        $this->json($result);
        return;
    }

    function POST_trashDraftDiligence()
    {
        $app = App::i();
        $diligence = $app->repo(EntityDiligence::class)->find($this->data['id']);
        $diligence->status = Entity::STATUS_TRASH;
        self::saveEntity($diligence);
        $this->json(['message' => 'success']);
    }
}
