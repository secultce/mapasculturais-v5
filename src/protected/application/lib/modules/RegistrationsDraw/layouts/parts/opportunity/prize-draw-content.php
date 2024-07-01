<?php
/** @var array{
 *    object{
 *      name: string,
 *      isDrawed: bool
 *    }
 * } $categories
 * @var array<string, \MapasCulturais\Entities\RegistrationsRanking[]> $rankings
 * @var bool $isAdmin
 * @var bool $isPublished
 */
?>
<div id="prize-draw">
    <?php $this->applyTemplateHook('opportunity-draw','begin'); ?>

    <?php if($isAdmin): ?>
    <h3>Sorteio</h3>
    <p class="new-ui-paragraph">
        Realize sorteio a partir de categorias para gerar listas com as pessoas selecionadas neste edital.
        <br>
        <span class="registration-help">Ex de uso: sorteie uma lista de pareceristas por categoria</span>
    </p>

    <div style="margin-top: 1.3rem;">
        <label class="new-ui-label categories-draw-label" for="categories-draw">Escolha uma categoria para sortear</label>
        <div style="display: flex; align-items: stretch">
            <select name="categories-draw" id="categories-draw" class="new-ui-select">
                <option disabled selected>Selecionar categoria</option>
                <?php if(empty($categories) || (count($categories) === 1 && $categories[0]->name == '')): ?>
                    <option value="">Todas as inscrições (não existem categorias cadastradas)</option>
                <?php else: foreach($categories as $category): ?>
                    <option <?= $category->isDrawed ? 'disabled' : '' ?>><?= $category->name ?></option>
                <?php endforeach; endif; ?>
            </select>
            <button class="btn btn-primary" id="draw-button">Realizar sorteio</button>
            <span id="draw-loading" style="display:none;"><img src="<?php $this->asset('img/spinner_192.gif') ?>"/></span>
        </div>
    </div>

    <hr>
    <?php endif; ?>

    <div id="rankings" style="background:#f5f5f5;padding:10px;">
        <h3>Sorteios realizados</h3>

        <label class="new-ui-label categories-draw-label" for="drawed-categories-filter">Filtrar por categoria sorteios já realizados</label>
        <div style="display: flex; align-items: stretch; justify-content: space-between; flex-wrap: wrap">
            <select id="drawed-categories-filter" class="new-ui-select">
                <option value="">Todas as categorias</option>
                <?php foreach ($categories as $category): if($category->isDrawed && $category->name != ''): ?>
                    <option><?= $category->name ?></option>
                <?php endif; endforeach; ?>
            </select>

            <div style="display:flex;flex-wrap:wrap;">
                <button class="btn btn-default">Baixar em PDF</button>
                <button class="btn btn-default" id="download-ranking">Baixar planilha (XLSX)</button>
                <?php if($isAdmin && !$isPublished): ?>
                <button class="btn btn-primary" id="pusblish-ranking">Publicar os sorteios</button>
                <?php elseif($isPublished): ?>
                <button class="btn published-draw-label">&#9989; Sorteios publicados</button>
                <?php endif; ?>
            </div>
        </div>

        <table class="js-registration-list registrations-table" id="ranking-table">
            <?php $this->applyTemplateHook('ranking-table','begin'); ?>
            <thead>
                <?php $this->applyTemplateHook('ranking-table--head','begin'); ?>
                <tr>
                    <th class="registration-id-col">Inscrição</th>
                    <th class="registration-option-col">Categoria</th>
                    <th class="registration-agents-col">Responsável</th>
                    <th class="registration-status-col">Ranking</th>
                </tr>
                <?php $this->applyTemplateHook('ranking-table--head','end'); ?>
            </thead>
            <tbody>
                <?php $this->applyTemplateHook('ranking-table--body','begin'); ?>
                <?php foreach($rankings as $category => $ranking):
                    $rankingList = $ranking['registrations'];
                    uasort($rankingList, function ($current, $next) {
                        return $current->rank > $next->rank;
                    });

                    foreach ($rankingList as $rank):
                ?>
                    <tr data-category="<?= $category ?>" class="approved">
                        <td>
                            <a href="/inscricao/<?= $rank->registration->id ?>"><?= $rank->registration->number ?></a>
                        </td>
                        <td><?= $category ?></td>
                        <td><a href="/agente/<?= $rank->registration->owner->id ?>"><?= $rank->registration->owner->name ?></a></td>
                        <td>#<?= $rank->rank ?></td>
                    </tr>
                <?php endforeach; endforeach; ?>

                <?php $this->applyTemplateHook('ranking-table--body','end'); ?>
            </tbody>
            <?php $this->applyTemplateHook('ranking-table','end'); ?>
        </table>
    </div>
</div>
