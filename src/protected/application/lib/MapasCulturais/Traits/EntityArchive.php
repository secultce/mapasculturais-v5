<?php
namespace MapasCulturais\Traits;
use MapasCulturais\App;

trait EntityArchive{

    /**
     * This entity uses Archive
     *
     * @return bool true
     */
    public static function usesArchive(){
        return true;
    }

    function getArchiveUrl(){
        return App::i()->createUrl($this->controllerId, 'archive', [$this->id]);
    }

    function getUnarchiveUrl(){
        return App::i()->createUrl($this->controllerId, 'unarchive', [$this->id]);
    }

    function archive($flush = true){
        $this->checkPermission('archive');
        
        $hook_class_path = $this->getHookClassPath();

        $app = App::i();
        $app->applyHookBoundTo($this, 'entity(' . $hook_class_path . ').archive:before');

        $this->setStatus(self::STATUS_ARCHIVED);
        
        $this->save($flush);

        if($this->usesFiles()){
            $this->makeFilesPrivate();
        }

        $app->applyHookBoundTo($this, 'entity(' . $hook_class_path . ').archive:before');
    }

    function unarchive($flush = true){
        $this->checkPermission('unarchive');
        
        $hook_class_path = $this->getHookClassPath();

        $app = App::i();
        $app->applyHookBoundTo($this, 'entity(' . $hook_class_path . ').unarchive:before');

        if ($this->usesDraft()) {
            $this->setStatus(self::STATUS_DRAFT);
        } else {
            $this->setStatus(self::STATUS_ENABLED);
        }

        $this->save($flush);
        
        if($this->usesFiles()){
            if($this->status === self::STATUS_ENABLED){
                $this->makeFilesPublic();
            }
        }


        $app->applyHookBoundTo($this, 'entity(' . $hook_class_path . ').unarchive:before');
    }

    /**
     * Criando a funcao de desabilitar aqui pois no momento somente via api desabilita uma entidade
     * Se houver a necessidade de criar essa função para o usuário, então, deve criar uma trait separada
     *
     * @param boolean $flush
     * @return void
     */
    function disabled($flush = true){
      
        $this->checkPermission('disabled');
        
        $hook_class_path = $this->getHookClassPath();

        $app = App::i();
       
        $app->applyHookBoundTo($this, 'entity(' . $hook_class_path . ').disabled:before');

        $this->setStatus(self::STATUS_DISABLED);

        $this->save($flush);

        if($this->usesFiles()){
            $this->makeFilesPrivate();
        }

        $app->applyHookBoundTo($this, 'entity(' . $hook_class_path . ').disabled:before');
    }
}
