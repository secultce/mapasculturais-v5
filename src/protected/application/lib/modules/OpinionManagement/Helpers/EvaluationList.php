<?php

namespace OpinionManagement\Helpers;

use Doctrine\ORM\Query\ResultSetMapping;
use MapasCulturais\App;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Entities\RegistrationEvaluation;
use MapasCulturais\Entities\User;

class EvaluationList implements \JsonSerializable
{

    /**
     * @var \MapasCulturais\Entities\Registration $registration
     */
    private $registration;
    /**
     * @var \MapasCulturais\Entities\RegistrationEvaluation[] $registrationEvaluations
     */
    public $registrationEvaluations;
    private $app;

    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
        $this->app = App::i();
        $this->registrationEvaluations = $this->app->repo(RegistrationEvaluation::class)
            ->findBy(['registration' => $this->registration->id]);

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('create_timestamp', 'create_timestamp');
        $rsm->addScalarResult('update_timestamp', 'update_timestamp');
        $rsm->addScalarResult( 'user_id', 'user_id');

        $query = $this->app->em
            ->createNativeQuery('SELECT
                e.registration_sent_timestamp create_timestamp
                ,e.registration_sent_timestamp update_timestamp
                ,e.valuer_user_id user_id
            FROM evaluations e
            WHERE registration_id = :r_id
                AND evaluation_id IS NULL', $rsm);
        $query->setParameter('r_id', $this->registration->id);
        $result = $query->getArrayResult();

        foreach ($result as $row) {
            $evaluation = new RegistrationEvaluation();
            $evaluation->registration = $this->registration;
            $evaluation->createTimestamp = $row['create_timestamp'];
            $evaluation->updateTimestamp = $row['update_timestamp'];
            $evaluation->user = $this->app->repo(User::class)->find($row['user_id']);

            $this->registrationEvaluations[] = $evaluation;
        }
    }


    public function jsonSerialize(): array
    {
        $data = array_filter($this->registrationEvaluations, function (RegistrationEvaluation $evaluation): bool {
            return $this->registration->canUser('viewUserEvaluation') || !is_null($evaluation->result);
        }, ARRAY_FILTER_USE_BOTH);

        return array_map(function (int $index, RegistrationEvaluation $evaluation): array {
            $evaluationSerialized = $evaluation->jsonSerialize();
            if(!$this->registration->canUser('viewUserEvaluation')) {
                $evaluationSerialized['agent'] = [
                    'id' => $index,
                    'name' => $index+1,
                ];
                $evaluationSerialized['singleUrl'] = $evaluationSerialized['registration']->singleUrl;
            }
            return $evaluationSerialized;
        }, array_keys($data), array_values($data));
    }
}