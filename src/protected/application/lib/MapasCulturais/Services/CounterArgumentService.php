<?php

namespace MapasCulturais\Services;

use DateTime;
use MapasCulturais\App;
use MapasCulturais\Entities\CounterArgument;
use MapasCulturais\Entities\CounterArgumentFile;
use MapasCulturais\Entities\CounterArgumentResponse;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Services\SentryService;
use MapasCulturais\Utils;

class CounterArgumentService
{
    private $counterArgumentEntity;
    private $counterArgumentResponseEntity;

    public function __construct()
    {
        $this->counterArgumentEntity = new CounterArgument();
        $this->counterArgumentResponseEntity = new CounterArgumentResponse();
    }

    public function isCounterArgumentPeriod($opportunity)
    {
        $initialStr = $opportunity->initialDateCounterArgument . ' ' . $opportunity->initialTimeCounterArgument;
        $finalStr = $opportunity->finalDateCounterArgument . ' ' . $opportunity->finalTimeCounterArgument;
        $initial = new DateTime($initialStr);
        $final = new DateTime($finalStr);
        $now = new DateTime();

        $appealEnabled = $opportunity->appealEnabled === 'Sim' ? true : false;

        if ($appealEnabled && $now >= $initial && $now <= $final) return true;

        return false;
    }

    public function isResponsePeriod($opportunity)
    {
        $finalStr = $opportunity->finalDateCounterArgument . ' ' . $opportunity->finalTimeCounterArgument;
        $final = new DateTime($finalStr);
        $now = new DateTime();

        $appealEnabled = $opportunity->appealEnabled === 'Sim' ? true : false;

        if ($appealEnabled && $now > $final) return true;

        return false;
    }

    public function getCounterArgumentsWithoutResponse($counterArguments)
    {
        $counterArgumentsWithoutResponse = array_filter($counterArguments, function ($counterArgument) {
            return !$counterArgument->response;
        });

        return $counterArgumentsWithoutResponse;
    }

    public function send(string $text, Registration $registration)
    {
        $this->counterArgumentEntity->text = $text;
        $this->counterArgumentEntity->registration = $registration;
        $this->counterArgumentEntity->save();

        $this->saveFiles($_FILES, $this->counterArgumentEntity);

        App::i()->em->flush();
    }

    public function update(string $text, CounterArgument $counterArgument)
    {
        $counterArgument->text = $text;
        $counterArgument->updateTimestamp = new DateTime();

        $this->saveFiles($_FILES, $counterArgument);

        $counterArgument->save(true);
        App::i()->em->flush();
    }

    private function saveFiles($files, $counterArgument)
    {
        App::i()->disableAccessControl();

        try {
            foreach ($files as $file) {
                $counterArgumentFile = new CounterArgumentFile($file);
                $counterArgumentFile->setGroup('counter-argument-attachment');
                $fileGroup = App::i()->getRegisteredFileGroup('contrarrazao', 'counter-argument-attachment');

                if ($fileGroup) {
                    $error = $fileGroup->getError($counterArgumentFile);
                    if ($error) {
                        throw new \RuntimeException($error);
                        SentryService::captureExceptions($e);
                    }
                } else {
                    Utils::validateFilesMimeType([$file], Utils::getAllowedUploadMimeTypes());
                }
                $counterArgumentFile->owner = $counterArgument;
                $counterArgumentFile->pridvate = true;
                $counterArgumentFile->save();
            }
        } finally {
            App::i()->enableAccessControl();
        }

    }

    public function removeFile(CounterArgumentFile $counterArgumentFile)
    {
        $counterArgumentFile->delete(true);
    }

    public function saveResponse(array $data)
    {
        $counterArgument = App::i()->repo('CounterArgument')->find($data['counterArgumentId']);
        $counterArgumentResponse = App::i()->repo('CounterArgumentResponse')->findOneBy(['counterArgument' => $counterArgument->id]);

        if ($counterArgumentResponse) {
            $counterArgumentResponse->text = $data['text'];
            $counterArgumentResponse->updateTimestamp = new DateTime();
            $counterArgumentResponse->save(true);
        } else {
            $this->counterArgumentResponseEntity->text = $data['text'];
            $this->counterArgumentResponseEntity->owner = App::i()->getUser()->profile;
            $this->counterArgumentResponseEntity->counterArgument = $counterArgument;
            $this->counterArgumentResponseEntity->save(true);
        }

        $counterArgument->status = (int)$data['status'];
        $counterArgument->save(true);
    }

    public function publishResponses($counterArguments)
    {
        foreach ($counterArguments as $counterArgument) {
            $response = $counterArgument->response;
            if ($response && !$response->published) {
                App::i()->disableAccessControl();
                $response->published = true;
                $response->save(true);
                App::i()->enableAccessControl();
            }
        }
    }
}
