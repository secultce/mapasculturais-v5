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
                <?php if(empty($categories)): ?>
                    <option value="">Todas as inscrições (não existem categorias cadastradas)</option>
                <?php else: foreach($categories as $category): ?>
                    <option><?= $category ?></option>
                <?php endforeach; endif; ?>
            </select>
            <button class="btn btn-primary" id="draw-button">Realizar sorteio</button>
        </div>
    </div>
</div>

<script>
    // @todo: Remover

    $('#draw-button').on('click', e => {
        e.preventDefault();

        const url = MapasCulturais.createUrl('sorteio-inscricoes', 'draw', [MapasCulturais.entity.id]);
        const category = $('#categories-draw').val();

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({category}).toString(),
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
            })
            .catch(error => {
                console.error(error);
            });
    });
</script>
