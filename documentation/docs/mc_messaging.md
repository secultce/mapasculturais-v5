# 🌎 Padronização para mensagaria
O Mapa atualmente tem um serviço para a mensageria [RabbitMQ](https://www.rabbitmq.com/) , sendo assim existe um arquivo de configuração chamado rabbitmq.php.

## 📝 Documentação
Todos os plugins e modulos deve seguir preferencialmente o padrão descrito na [documentação](https://secult-ceara-1.gitbook.io/secult-ceara-docs/documentacao) para todos os produtores e consumidores.

## 🔧 Arquivos

 1. Serviço: [AmqpQueueService](https://github.com/secultce/mapasculturais-v5/tree/main/src/protected/application/lib/MapasCulturais/Services/AmqpQueueService.php)
 2. Conf. Rabbitmq: [rabbitmq.php](https://github.com/secultce/mapasculturais-v5/tree/main/src/protected/application/conf/conf-base.d/rabbitmq.php)

## 🔧 Plugins e modulos
Abaixo consta dos plugins e modulos que fazem uso desse serviço

 - [Recurso](https://github.com/secultce/plugin-Recourse)
 - [Publicar parecers](https://github.com/secultce/plugin-OpinionManagement)
 - [Modulo fase de oportunidades](https://github.com/secultce/mapasculturais-v5/blob/main/src/protected/application/lib/modules/OpportunityPhases/Module.php) 