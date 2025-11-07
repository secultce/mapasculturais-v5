<?php
namespace CounterReason\Controllers;

use CounterReason\Entities\CounterReason as CounterReasonEntity;
use CounterReason\Services\CounterReasonService;
use MapasCulturais\App;
use Carbon\Carbon;


class Controller extends \MapasCulturais\Controller
{

    public function GET_index()
    {

    }

    public function POST_save()
    {
        $app = App::i();

        try {
            $entity = CounterReasonService::create($app, $this);
            $this->json(['message' => 'success', 'status' => 200, 'entityId' => $entity->]);
        }catch (\Exception $e){
            dump($e);
        }


    }
}
