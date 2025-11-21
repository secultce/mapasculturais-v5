<?php

namespace MapasCulturais\Services;

use MapasCulturais\App;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Entities\RegistrationMeta;

class BonificationB2Service
{
    const PREFIX_B2 = 'b2_';
    const PREFIX_BONUS = 'bonus_';
    const PREFIX_STATUS = 'bonificated_field_';

    protected $app;
    protected $registration;
    protected $bonusAmount;
    protected $evaluationMethod;
    protected $opportunity;
    protected $consolidatedResult;

    public function __construct($registration, $bonusAmount, $consolidatedResult)
    {
        $this->app = App::i();
        $this->registration = $registration;
        $this->opportunity = $registration->opportunity;
        $this->evaluationMethod = $this->opportunity->getEvaluationMethod();
        $this->bonusAmount = $bonusAmount;
        $this->consolidatedResult = $consolidatedResult;
    }

    public function process(&$result)
    {
        $registration = $this->registration;
        $app = $this->app;
        $em = $this->evaluationMethod;
        $bonusAmount = $this->bonusAmount;
        $consolidated_result = $this->consolidatedResult;

        $evaluator = $registration->opportunity->getEvaluationCommittee(false);

        $canEvaluate = [];
        $registrationEvaluations = $app->repo('RegistrationEvaluation')->findBy([
            'registration' => $registration
        ]);

        foreach ($evaluator as $valuer) {
            if ($em->canUserEvaluateRegistration($registration, $valuer->user)) {
                $canEvaluate[] = $valuer;
            }
        }

        $canEvaluateCount = count($canEvaluate);
        $b2TruePerEvaluation = [];
        $foundFalse = [];
        $allB2Keys = [];

        foreach ($registrationEvaluations as $i => $evaluation) {
            $data = $evaluation->evaluationData;
            $foundInThisEval = [];

            foreach ($data as $key => $value) {
                if (strpos($key, self::PREFIX_B2) === 0 && $value === 'true') {
                    $foundInThisEval[] = $key;
                    $allB2Keys[$key] = true;
                }

                if ($value === 'false') {
                    $foundFalse[] = $key;
                }
            }

            $b2TruePerEvaluation[] = $foundInThisEval;
        }


        if (!empty($foundFalse)) {
            App::i()->disableAccessControl();

            $totalToSubtract = 0;
            $deletedCount = 0;

            foreach ($foundFalse as $b2Key) {
                $metaKey = self::PREFIX_BONUS . $b2Key;
                $statusKey = self::PREFIX_STATUS . self::PREFIX_BONUS . $b2Key;

                $existingBonus = $app->repo('RegistrationMeta')->findBy([
                    'key' => $metaKey,
                    'owner' => $registration
                ]);

                $existingStatus = $app->repo('RegistrationMeta')->findBy([
                    'key' => $statusKey,
                    'owner' => $registration
                ]);

                if ($existingBonus && $existingStatus) {

                    foreach ($existingStatus as $status) {
                        $status->delete(true);
                    }

                    foreach ($existingBonus as $bonus) {
                        $bonus->delete(true);
                    }

                    $totalToSubtract += $bonusAmount;
                    $deletedCount++;

                }
            }

            if ($deletedCount > 0) {
                $result = floatval($consolidated_result) - $totalToSubtract;
                App::i()->enableAccessControl();
                return $result;
            }

            App::i()->enableAccessControl();
        }

        $consensusB2 = array_keys($allB2Keys);

        foreach ($b2TruePerEvaluation as $evalKeys) {
            $consensusB2 = array_intersect($consensusB2, $evalKeys);
        }

        if (
            count($registrationEvaluations) === $canEvaluateCount &&
            !empty($consensusB2) &&
            $registration->canUser('evaluate')
        ) {
            App::i()->disableAccessControl();

            foreach ($consensusB2 as $b2Key) {
                $metaKey = self::PREFIX_BONUS . $b2Key;

                $bonusData = $app->repo('RegistrationMeta')->findBy([
                    'key' => $metaKey,
                    'owner' => $registration
                ]);

                $alreadyBonificated = $app->repo('RegistrationMeta')->findBy([
                    'key' => self::PREFIX_STATUS . $metaKey,
                    'owner' => $registration
                ]);

                if (!$bonusData && empty($alreadyBonificated)) {
                    $regMeta = new RegistrationMeta();
                    $regMeta->key = $metaKey;
                    $regMeta->value = $bonusAmount;
                    $regMeta->owner = $registration;
                    $regMeta->save(true);

                    $bonusStatusMeta = new RegistrationMeta();
                    $bonusStatusMeta->key = self::PREFIX_STATUS . $metaKey;
                    $bonusStatusMeta->value = 'true';
                    $bonusStatusMeta->owner = $registration;
                    $bonusStatusMeta->save(true);

                    $result = floatval($consolidated_result) + $bonusAmount * count($consensusB2);
                    return $result;
                }
            }

            $result = $consolidated_result;
            App::i()->enableAccessControl();
        }

        return $result;
    }
}
