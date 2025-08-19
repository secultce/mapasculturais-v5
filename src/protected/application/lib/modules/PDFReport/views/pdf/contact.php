<?php

$this->layout = 'nolayout';
$contact = $app->view->jsObject['subscribers'];
$nameOpportunity = $contact[0]->opportunity->name;

include_once('header.php');

?>

<div class="container">
    <table class="table table-striped table-bordered">
        <thead>
            <tr style="background-color: #009353;">
                <th class="td-classificacao" style="width:20%;border: 1px solid #716e6e; ">Inscrição</th>
                <th class="td-classificacao" style="width:40%; border: 1px solid #716e6e;">Nome</th>
                <th class="td-classificacao" style="width:40%; border: 1px solid #716e6e;">E-mail</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <?php foreach ($contact as $key => $value) : ?>
            <tr>
                <td class="td-classificacao">
                    <?php echo $value->number; ?>
                </td>
                <td class="td-classificacao ">
                    <?php echo $value->owner->name; ?>
                </td>
                <td>
                    <?php
                    if (!isset($value->owner->metadata['emailPrivado'])) {
                        echo "";
                    } else {
                        echo $value->owner->metadata['emailPrivado'];
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include_once('footer.php'); ?>
