<?php

namespace MapasCulturais\Services;

use DateTime;
use MapasCulturais\App;
use MapasCulturais\Entities\CounterArgument;
use MapasCulturais\Entities\CounterArgumentFile;
use MapasCulturais\Entities\CounterArgumentResponse;
use MapasCulturais\Entities\Registration;

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

    public function send(string $text, Registration $registration)
    {
        $this->counterArgumentEntity->text = $text;
        $this->counterArgumentEntity->registration = $registration;
        $this->counterArgumentEntity->save();

        $this->saveFiles($_FILES, $this->counterArgumentEntity);

        App::i()->em->flush();
    }

    public function update(array $data)
    {
        $counterArgument = App::i()->repo('CounterArgument')->find($data['id']);
        $counterArgument->text = $data['text'];
        $counterArgument->updateTimestamp = new DateTime();

        $this->saveFiles($_FILES, $counterArgument);

        $counterArgument->save(true);
        App::i()->em->flush();
    }

    private function saveFiles($files, $counterArgument)
    {
        foreach ($files as $file) {
            App::i()->disableAccessControl();

            $counterArgumentFile = new CounterArgumentFile($file);
            $counterArgumentFile->setGroup('counter-argument-attachment');
            $counterArgumentFile->owner = $counterArgument;
            $counterArgumentFile->private = true;
            $counterArgumentFile->save();

            App::i()->enableAccessControl();
        }
    }

    public function removeFile(int $fileId)
    {
        $counterArgumentFile = App::i()->repo('CounterArgumentFile')->find($fileId);
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
}
