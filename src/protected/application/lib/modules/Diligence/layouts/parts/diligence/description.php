<p style="font-weight:bold;font-size: 14px;margin-bottom: 1.14em;">Escrever diligência</p>
<fieldset class="diligence-fieldset">
    <p>Assunto da diligência</p>
    <label>
        <input type="radio" name="diligence-type" value="0">
        Execução física do objeto
    </label>
    <label>
        <input type="radio" name="diligence-type" value="1">
        Relatório financeiro
    </label>
</fieldset>
<textarea name="description" id="descriptionDiligence" cols="30" rows="10" placeholder="<?= $placeHolder; ?>" class="diligence-context-open"></textarea>
<input type="hidden" id="id-input-diligence">
