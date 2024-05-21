<?php if($this->isEditable()): ?>
    <div class="registration-fieldset">
        <p>
            <span class="label">Dias úteis para resposta da diligência:</span>
            <span class="js-editable" data-edit="diligence_days" data-original-title="Público presente" data-emptytext="Selecione">
                <?php echo $entity->diligence_days; ?>
            </span>
        </p>
        <p class="registration-help ng-scope">
            Informe o total de dias úteis que o proponente terá para dá uma resposta
            a diligência enviada para ele.
        </p>
    </div>

<?php endif; ?>