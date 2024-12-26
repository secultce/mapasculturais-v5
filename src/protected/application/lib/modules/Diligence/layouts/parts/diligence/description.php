<?php
$function = "";
if($isProponent){
    $function = "saveAnswerProponente(0)";
}else{
    $function = "saveDiligence(0, 0, $('#id-input-diligence').val())";
}
?>
<textarea name="description" id="descriptionDiligence" cols="30" rows="10" placeholder="<?= $placeHolder; ?>" class="diligence-context-open"></textarea>
<input type="hidden" id="id-input-diligence">
