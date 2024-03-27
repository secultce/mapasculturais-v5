<?php

use function MapasCulturais\__exec;

$app = MapasCulturais\App::i();
$em = $app->em;
$conn = $em->getConnection();

return [
    'create table diligence' => function(){
        __exec("CREATE SEQUENCE diligence_id_seq INCREMENT BY 1 MINVALUE 1 START 1;");

        __exec("CREATE TABLE IF NOT EXISTS diligence (
            id INT NOT NULL, 
            registration_id INT NOT NULL, 
            open_agent_id INT NOT NULL, 
            agent_id VARCHAR(32) NOT NULL,
            create_timestamp timestamp,
            description TEXT,
            status INT NOT NULL,
            situation INT NULL,
            days integer NULL,
            enable boolean NULL default false,
            PRIMARY KEY(id));");

        __exec("ALTER TABLE diligence ADD
        CONSTRAINT diligence_registration_fk
        FOREIGN KEY (registration_id) REFERENCES registration (id)
        ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");

        __exec("ALTER TABLE diligence ADD
        CONSTRAINT diligence_open_agent_id_fk
        FOREIGN KEY (open_agent_id) REFERENCES agent (id)
        ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");

        // $conn->executeQuery("UPDATE evaluation_method_configuration SET type = 'documentary' WHERE opportunity_id IN ({$ids})");
    }
];