<?php

use PDFReport\Controllers\Pdf;

?>

<div>
    <hr>
    <form action="<?php echo $app->createUrl('pdf/gerarPdf'); ?>" method="GET" target="TargetWindow">
        <label class="label">Filtrar Relatório</label>
        <select name="selectRel" id="selectRel" class="" style="margin-left: 10px;" required>
            <option value="">--Selecione--</option>
            <?php foreach (Pdf::getReportsEnabled() as $report): ?>
                <option value="<?= $report['id'] ?>"><?= $report['title'] ?></option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" id="idopportunityReport" name="idopportunityReport">
        <button type="submit">Gerar Relatório <i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>
    </form>
</div>
<div>
    <?php
    $entity = $this->controller->requestedEntity;
    $url = $app->createUrl('oportunidade/' . $entity->id);

    if (isset($_SESSION['error'])) {
        echo '
            <hr>
            <div class="alert danger">' . $_SESSION['error'] . '
                <a href="' . $url . '" class="alignright">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </a>
            </div>
        ';
    }

    unset($_SESSION['error']);
    ?>
</div>
