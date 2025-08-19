<?php

$this->layout = 'nolayout-pdf';
$sub = $app->view->jsObject['subscribers'];
$nameOpportunity = $sub[0]->opportunity->name;
$op = $app->view->jsObject['opp'];

$isCategory = false;
if (is_array($op->registrationCategories) && count($op->registrationCategories) > 0) {
    $isCategory = true;
}

?>

<div class="container">
    <?php include_once('header-pdf.php'); ?>
    <div class="container">
        <div class="pre-text">Relação de Inscritos</div>
        <div class="opportunity-info">
            <p>Oportunidade: </p>
            <h4><?php echo $nameOpportunity ?></h4>
        </div>
    </div>
    <div class="row" style="margin-top: 20px">
        <div class="container">
            <?php
            if ($isCategory) :
                foreach ($op->registrationCategories as $key_first => $nameCat) : ?>
                    <div class="table-info-cat" style="margin-top: 10px">
                        <span><?php echo $nameCat; ?></span>
                    </div>
                    <table id="table-preliminar" width="100%" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-left" width="20%">INSCRIÇÃO</th>
                                <th class="text-left">CANDIDATOS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $arrayCheck = [];
                            foreach ($sub as $key => $value) {
                                $agent = $app->repo('Agent')->find($value->owner->id);
                                if ($nameCat == $value->category) {
                                    $arrayCheck[] = $value->category; ?>
                                    <tr>
                                        <td class="text-left"><?php echo $value->number; ?></td>
                                        <td class="text-left"><?php echo strtoupper($agent->name); ?></td>

                                    </tr>
                                <?php
                                }
                            }
                            if (!in_array($nameCat, $arrayCheck)) { ?>
                                <tr>
                                    <td class="text-left"></td>
                                    <td class="text-left">Não há candidatos inscritos nessa categoria</td>
                                    <td class="text-center"></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php endforeach;
            else: ?>
                <table id="table-preliminar" width="100%" class="table table-striped table-bordered">
                    <thead>
                        <tr style="border: 1px solid #c3c3c3;" class="table-info-cat">
                            <th class="text-left" style="padding-left: 5px; color: #fff;" width="20%">INSCRIÇÃO</th>
                            <th class="text-left" style="color: #fff;">CANDIDATOS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($sub as $key => $value) {
                            $agent = $app->repo('Agent')->find($value->owner->id);
                        ?>
                            <tr>
                                <td class="text-left"><?php echo $value->number; ?></td>
                                <td class="text-left"><?php echo $agent->name; ?></td>

                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>
