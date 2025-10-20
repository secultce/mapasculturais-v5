<?php if (!$opportunity->publishedOpinions || !$opportunity->publishedRegistrations): ?>
    <div id="opinions-config" class="registration-fieldset">
        <h4>Publicação de Pareceres</h4>
        <p>Deseja que os pareceres desta fase/oportunidade sejam publicados para os proponentes automaticamente ao publicar os resultados?</p>
        <span
            class="js-editable editable editable-click"
            data-edit="autopublishOpinions"
            data-original-title="Publicar pareceres automaticamente"
            data-value="<?= $opportunity->getMetadata('autopublishOpinions') ?>"
        >
            <?= $opportunity->getMetadata('autopublishOpinions') ?>
        </span>
        <br><br>
        <em>Caso marque "Não" aparecerá um botão para publicar pareceres manualmente na aba de inscrições.</em>
    </div>
<?php endif; ?>