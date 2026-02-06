<?php

namespace MapasCulturais\Services;

use DateTime;
use MapasCulturais\App;
use MapasCulturais\Entities\CounterArgument;
use MapasCulturais\Entities\CounterArgumentFile;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Utils;

class CounterArgumentService
{
    private $counterArgumentEntity;

    public function __construct()
    {
        $this->counterArgumentEntity = new CounterArgument();
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

            $fileGroup = App::i()->getRegisteredFileGroup('contrarrazao', 'counter-argument-attachment');

            if ($fileGroup) {
                $error = $fileGroup->getError($counterArgumentFile);
                if ($error) {
                    App::i()->enableAccessControl();
                    throw new \RuntimeException($error);
                }
            } else {
                Utils::validateFilesMimeType([$file], Utils::getAllowedUploadMimeTypes());
            }

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
}
