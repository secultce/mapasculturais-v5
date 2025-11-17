<?php

namespace CounterReason\Services;

use Carbon\Carbon;
use MapasCulturais\App;
use CounterReason\Entities\CounterReason;
use CounterReason\Traits\FileUploadTrait;
use MapasCulturais\Entities\Registration;
use CounterReason\Entities\CounterReasonFile;
use MapasCulturais\Exceptions\FileUploadError;
use CounterReason\Repositories\CounterReasonRepository;
use CounterReason\Entities\CounterReason as CounterReasonEntity;

class CounterReasonService
{
    use FileUploadTrait;
    protected function getFileClassName(): string
    {
        return CounterReasonFile::class;
    }
    static public function create(App $app, $data): CounterReason
    {
        $app->disableAccessControl();
        $registration = $app->repo('Registration')->find($data['registration']);
        $entity = new CounterReasonEntity();
        $entity->text           = $data['text'] ?? null;
        $entity->send           = Carbon::now();
        $entity->status         = $data->data['status'] ?? 1; // ou outro status padrão
        $entity->registration   = $registration;
        $entity->opportunity    = $registration->opportunity;
        $entity->agent          = $registration->owner;
   
        $entity->save(true);
        foreach ($_FILES as $file) {
            $newFile = new CounterReasonFile($file);
            $newFile->setGroup('cr-attachment');
            $newFile->owner = $entity;
            $newFile->makePrivate();
        }
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
      
        foreach ($_FILES as $file) {         
            $newFile = new CounterReasonFile($file);            
            $newFile->setGroup('recourse-attachment');
            $newFile->owner = $cr;
            $newFile->makePrivate();
        }

        $app->enableAccessControl();
        return $cr;
    }


    /**
     * Retorna o nome da classe File do módulo
     */
    // abstract protected function getFileClassName(): string;
}