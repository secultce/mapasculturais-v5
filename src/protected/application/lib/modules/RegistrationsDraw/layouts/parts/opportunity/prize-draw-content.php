<?php
/** @var string[] $categories
 * @var array<string, \MapasCulturais\Entities\Draw[]> $rankings
 * @var bool $isAdmin
 * @var bool $isPublished
 * @var \MapasCulturais\Controllers\Opportunity $opportunity
 * @var \MapasCulturais\App $app
 */
?>
<div id="prize-draw">
    <?php $this->applyTemplateHook('opportunity-draw', 'begin'); ?>

    <?php if ($isAdmin) : ?>
    <h3>Sorteio</h3>
    <p class="new-ui-paragraph">
        Realize sorteio a partir de categorias para gerar listas com as pessoas selecionadas neste edital.
        <br>
        <span class="registration-help">Ex de uso: sorteie uma lista de pareceristas por categoria</span>
    </p>

    <div style="margin-top: 1.3rem;">
        <label class="new-ui-label categories-draw-label" for="categories-draw">
            Escolha uma categoria para sortear
        </label>
        <div style="display: flex; align-items: stretch">
            <select name="categories-draw" id="categories-draw" class="new-ui-select">
                <option disabled selected>Selecionar categoria</option>
                <?php if (empty($categories) || (count($categories) === 1 && $categories[0] == '')) : ?>
                    <option value="">Todas as inscrições (não existem categorias cadastradas)</option>
                <?php else :
                    foreach ($categories as $category) : ?>
                        <option><?= $category ?></option>
                    <?php endforeach;
                endif; ?>
            </select>
            <button class="btn btn-primary" id="draw-button">Realizar sorteio</button>
            <span id="draw-loading" style="display:none;">
                <img src="<?php $this->asset('img/spinner_192.gif') ?>" alt="Loading..."/>
            </span>
        </div>
    </div>

    <hr>
    <?php endif; ?>

    <div id="rankings" style="background:#f5f5f5;padding:10px;">
        <h3>Sorteios realizados</h3>

        <div class="draw-table-title">
            <div>
                <label class="new-ui-label" style="">Baixar todos os sorteios</label>
                <div style="display:flex; justify-content: space-between; margin-top: 8px">
                    <!-- @todo: Mandar para '/sorteio-inscricoes/pdf/{id}' -->
                    <button class="btn btn-default" id="download-ranking">Em PDF</button>
                    <button class="btn btn-default" id="download-ranking">Em .XLSX</button>
                </div>
            </div>
            <?php if ($isAdmin && !$isPublished) : ?>
                <button class="btn btn-primary" id="pusblish-ranking">Publicar os sorteios</button>
            <?php elseif ($isPublished) : ?>
                <button class="btn published-draw-label">&#9989; Sorteios publicados</button>
            <?php endif; ?>
        </div>

        <div style="margin-top: 24px; display:flex; gap: 1em;">
            <div class="draw-history">
                <label class="new-ui-label categories-draw-label" for="history-categories-filter">
                    Filtrar por categoria
                </label>
                <select id="history-categories-filter" class="new-ui-select">
                    <option value="">Selecionar categoria</option>
                    <?php foreach ($categories as $category) :
                        if ($category != '') : ?>
                            <option><?= $category ?></option>
                        <?php endif;
                    endforeach; ?>
                </select>

                <div class="history-list">
                    <?php foreach ($rankings as $category => $draws) : ?>
                        <div class="category-list" data-category="<?= $category ?>">
                            <span class="history-category"><?= $category ?></span>
                            <?php foreach ($draws as $draw) : ?>
                                <label
                                    for="draw-content"
                                    data-draw-id="<?= $draw->id ?>"
                                    data-category-name="<?= $category ?>"
                                    onclick="filterTableRows(this)"
                                >
                                    Sorteio realizado em:
                                    <span><?= $draw->createTimestamp->format('d/m/Y \à\s H:i:s') ?><span>
                                </label>
                            <?php endforeach ;?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="draw-content" id="draw-content">
                <h5>Lista sorteada para a categoria
                    <strong id="current-category-name"><?= $categories[0] ?></strong>
                </h5>
                <div>
                    <span>Baixar sorteio da categoria: </span>
                    <!-- @todo: Mandar para '/sorteio-inscricoes/pdf/{id}' -->
                    <button class="btn btn-default" id="download-ranking">Em PDF</button>
                    <button class="btn btn-default" id="download-ranking">Em .XLSX</button>
                </div>
                <table class="js-registration-list registrations-table" id="ranking-table">
                    <?php $this->applyTemplateHook('ranking-table', 'begin'); ?>
                    <thead>
                        <?php $this->applyTemplateHook('ranking-table--head', 'begin'); ?>
                        <tr>
                            <th class="registration-status-col">Ranking</th>
                            <th class="registration-id-col">Inscrição</th>
                            <th class="registration-agents-col">Responsável</th>
                            <th class="registration-agents-col">Status</th>
                        </tr>
                        <?php $this->applyTemplateHook('ranking-table--head', 'end'); ?>
                    </thead>
                    <tbody>
                        <?php $this->applyTemplateHook('ranking-table--body', 'begin'); ?>
                        <?php foreach ($rankings as $category => $draws) :
                            foreach ($draws as $draw) :
                                $rankingList = $draw->drawRegistrations->toArray();
                                uasort($rankingList, function ($current, $next) {
                                    return $current->rank > $next->rank;
                                });
                                foreach ($rankingList as $rank) : ?>
                                    <tr data-draw-id="<?= $draw->id ?>" class="approved" style="display: none">
                                        <td>#<?= $rank->rank ?></td>
                                        <td>
                                            <a href="/inscricao/<?= $rank->registration->id ?>">
                                                <?= $rank->registration->number ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="/agente/<?= $rank->registration->owner->id ?>">
                                                <?= $rank->registration->owner->name ?>
                                            </a>
                                        </td>
                                        <td>Selecionado</td>
                                    </tr>
                                <?php endforeach;
                            endforeach;
                        endforeach; ?>

                        <?php $this->applyTemplateHook('ranking-table--body', 'end'); ?>
                    </tbody>
                    <?php $this->applyTemplateHook('ranking-table', 'end'); ?>
                </table>
            </div>
        </div>
    </div>
</div>
