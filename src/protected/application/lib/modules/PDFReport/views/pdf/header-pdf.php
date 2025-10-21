<?php

$this->layout = 'nolayout-pdf';
$reg = $app->view->regObject['ins'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link type="text/css" href="<?php $this->asset('css/stylePdfReport.css') ?>" rel="stylesheet" />
</head>

<body>
    <table width="100%" style="height: 100px;">
        <thead>
            <tr class="">
                <td style="text-align: center">
                    <img src="<?php echo MODULES_PATH . 'PDFReport/assets/img/logoNova.png'; ?>" height="96px" />
                </td>
            </tr>
        </thead>
    </table>
