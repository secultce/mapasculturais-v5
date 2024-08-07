<?php

namespace MapasCulturais\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use MapasCulturais\Repository;

class Draw extends Repository
{
    private $app;

    /** @override */
    public function __construct(EntityManagerInterface $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->app = \MapasCulturais\App::getInstance();
    }
}
