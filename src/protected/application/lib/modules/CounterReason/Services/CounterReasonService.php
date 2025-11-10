<?php

namespace CounterReason\Services;

use CounterReason\Entities\CounterReason;
use CounterReason\Entities\CounterReason as CounterReasonEntity;
use Carbon\Carbon;
use CounterReason\Repositories\CounterReasonRepository;
use MapasCulturais\App;
use MapasCulturais\Entities\Registration;

class CounterReasonService
{
    static public function create(App $app, $data): CounterReason
    {
        $app->disableAccessControl();
        $registration = $app->repo('Registration')->find($data->data['registration']);
        $entity = new CounterReasonEntity();
        $entity->text = $data->data['text'] ?? null;
        $entity->send = Carbon::now();
        $entity->status = $data->data['status'] ?? 0 ; // ou outro status padrão
        $entity->registration = $registration;
        $entity->opportunity = $registration->opportunity;
        $entity->agent = $registration->owner;
        $entity->save(true); // true = flush imediato
        $app->enableAccessControl();
        return $entity;
    }

    /**
     * @param Registration $registration
     * @param App $app
     * @param $data
     * @return void
     */
    static public function update(Registration $registration, App $app, $data): CounterReason
    {

        // Atualiza data de envio (ou edição)
        $cr = CounterReasonRepository::getCounterReason($registration, $app);
        $cr->text = $data['text'];
        $cr->send = Carbon::now();
        $app->disableAccessControl();
        $cr->save(true);
        $app->enableAccessControl();
        return $cr;
    }
}
