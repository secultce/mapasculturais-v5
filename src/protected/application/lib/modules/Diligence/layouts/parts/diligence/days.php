<?php if($this->isEditable()): ?>
    <div class="registration-fieldset">
        <p>
            <span class="label">Dias corridos para resposta da diligência:</span>
            <span class="js-editable" data-edit="diligence_days" data-original-title="Público presente" data-emptytext="Selecione">
                <?php echo $entity->diligence_days; ?>
            </span>
        </p>
        <p class="registration-help ng-scope">
            Informe o total de dias corrridos que o proponente terá para dá uma resposta
            a diligência enviada para ele.
        </p>
    </div>

<?php endif; ?>