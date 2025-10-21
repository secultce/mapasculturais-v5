<body>
<div id="atribuir">
    <label for="search-input" style="font-weight: bold; font-size: 1rem">Busque por agentes culturais</label>
    <p>Você pode buscar um agente individualmente ou utilizar vários CPFs e nomes para encontrá-los</p>

    <label for="search-input" class="input-container" >
        <span class="search-input">
            <input id="search-input" placeholder="Busque por CPF ou nome" width="100%" style="border: none; outline: none" />
        </span>
        <button class="clear-button">Limpar pesquisas</button>
    </label>
    <input type="hidden" name="search-values" value="[]">

    <div id="agents-list-container" style="display: block; margin-top: 2rem">
        <h4>Lista de agentes cotistas</h4>
        <div class="search-list-header">
            <h6 style="font-size: 2rem">Agentes encontrados</h6>
            <div style="display: flex; align-items: center; max-width: 100%;justify-content:space-between;">
                <strong>Utilize o botão para atribuir cotas a todos os agentes na lista abaixo.</strong>
                <button style="width: 30%;" class="btn btn-primary" id="bulk-assign-button" onclick="bulkAssignQuota(this)">Atribuir cota racial em lote</button>
            </div>
        </div>
        <hr>
        <div class="agents-list">
            <input type="hidden" name="result-ids" value="[]">
            <div class="export-buttons" id="results-export-buttons">
                <button style="background-color: #076d21; " class="btn" id="export-results-csv">Exportar Resultados para CSV</button>
            </div>
            <table id="agent-results-table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>CPF</th>
                        <th>Nome</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="export-buttons" id="assigned-export-buttons">
                <button style="background-color: #076d21; " class="btn" id="export-assigned-csv">Exportar Resultados para CSV</button>
            </div>
            <table id="assigned-agents-table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>CPF</th>
                        <th>Nome</th>
                        <th>Cotas</th>
                        <th>Período</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <p>Ainda não possuem agentes culturais com cotas atribuídas.</p>
            <p><strong>Utilize a busca para encontrar agentes culturais</strong></p>
        </div>
    </div>
</div>