<?php

namespace Diligence\Service;
use \Diligence\Controllers\Controller;

interface DiligenceInterface {

    /**
     * Cria uma pergunta ou resposta
     *
     * @param [object] $class
     * @return void
     */
    public function create($class);

    /**
     * Injeção de dependência do controle do modulo
     *
     * @param Controller $class
     * @return void
     */
    public function cancel(Controller $class);
}