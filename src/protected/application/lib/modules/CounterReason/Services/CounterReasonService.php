<?php

namespace CounterReason\Services;

use CounterReason\Entities\CounterReason;
use CounterReason\Entities\CounterReason as CounterReasonEntity;
use Carbon\Carbon;
use MapasCulturais\App;

class CounterReasonService
{
    static public function create(App $app, $data)
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
}
