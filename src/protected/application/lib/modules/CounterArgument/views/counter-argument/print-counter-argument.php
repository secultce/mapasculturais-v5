<?php

use MapasCulturais\App;
use MapasCulturais\Entities\CounterArgument as EntityCounterArgument;

$counterArgument = App::i()->repo(EntityCounterArgument::class)->find($this->controller->data["counterArgumentId"]);

require THEMES_PATH . 'BaseV1/layouts/headpdf.php';

?>

<main>
    <h4><?= $counterArgument->registration->opportunity->name ?></h4>
    <h4>PEDIDO DE CONTRARRAZÃO</h4>

    <table>
        <tr>
            <td style="width: 30%;"><b>NÚMERO DE INSCRIÇÃO</b></td>
            <td><?= $counterArgument->registration->number ?></td>
        </tr>
        <tr>
            <td><b>PROJETO</b></td>
            <td><?= $counterArgument->registration->opportunity->ownerEntity->name ?></td>
        </tr>
		<?php if ($counterArgument->registration->category): ?>
        <tr>
            <td><b>CATEGORIA</b></td>
            <td><?= $counterArgument->registration->category ?></td>
        </tr>
		<?php endif; ?>
        <tr>
            <td><b>PROPONENTE</b></td>
			<td><?= $counterArgument->registration->owner->getMetadata('nomeSocial') ?: $counterArgument->registration->owner->name ?></td>
        </tr>
        <tr>
            <td><b>MOTIVOS DA CONTRARRAZÃO</b></td>
            <td>REVISÃO DA AVALIAÇÃO DE MÉRITO CULTURAL</td>
        </tr>
        <tr>
            <th colspan="2"><b>PEDIDO DE CONTRARRAZÃO (Mapa Cultural)</b></th>
        </tr>
    </table>
    <div class="counter-argument-text"><?= $counterArgument->text ?></div>
    <table>
        <tr>
            <th colspan="2"><b>RESPOSTA DA COMISSÃO</b></th>
        </tr>
    </table>
    <div class="counter-argument-text"><?= $counterArgument->response ? $counterArgument->response->text : 'Sem resposta' ?></div>
    <table style="margin-bottom: 50px;">
        <tr>
            <td><b>RESULTADO</b></td>
            <td><?= EntityCounterArgument::STATUSES[$counterArgument->status]; ?></td>
        </tr>
    </table>
</main>

<?php require THEMES_PATH . 'BaseV1/views/pdf/footer-pdf.php'; ?>
