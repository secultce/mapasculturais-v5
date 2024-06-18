<?php
/** @var array{
 *    object{
 *      name: string,
 *      isDrawed: bool
 *    }
 * } $categories
 */
?>
<div id="prize-draw">
    <?php $this->applyTemplateHook('opportunity-draw','begin'); ?>
    <h3 style="font-family: 'Open Sans'; font-weight: 700">Sorteio</h3>
    <p class="new-ui-paragraph">
        Realize sorteio a partir de categorias para gerar listas com as pessoas selecionadas neste edital.
        <br>
        <span class="registration-help">Ex de uso: sorteie uma lista de pareceristas por categoria</span>
    </p>

    <div style="margin-top: 1.3rem;">
        <label class="new-ui-label" for="categories-draw" id="categories-draw-label">Escolha uma categoria para sortear</label>
        <div style="display: flex; align-items: stretch">
            <select name="categories-draw" id="categories-draw" class="new-ui-select">
                <option disabled selected>Selecionar categoria</option>
                <?php foreach($categories as $category): ?>
                    <option><?= $category ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary">Realizar sorteio</button>
        </div>
    </div>
</div>
