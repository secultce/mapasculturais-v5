<?php

namespace MapasCulturais\Services;

use DateTime;
use MapasCulturais\App;
use MapasCulturais\Entities\CounterArgument;
use MapasCulturais\Entities\CounterArgumentFile;

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

    public function send(array $data)
    {
        $registration = App::i()->repo('Registration')->find($data['registration']);
        $this->counterArgumentEntity->text = $data['text'];
        $this->counterArgumentEntity->registration = $registration;
        $this->counterArgumentEntity->save();

        foreach ($_FILES as $file) {
            App::i()->disableAccessControl();

            $counterArgumentFile = new CounterArgumentFile($file);
            $counterArgumentFile->setGroup('counter-argument-attachment');
            $counterArgumentFile->owner = $this->counterArgumentEntity;
            $counterArgumentFile->private = true;
            $counterArgumentFile->save();

            App::i()->enableAccessControl();
        }

        App::i()->em->flush();
    }
}
