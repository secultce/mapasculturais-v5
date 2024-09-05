<style>
    #atribuir .search-input {
        padding: .5rem;
        margin-top: .6rem;
        min-height: 3rem;
        min-width: 50rem;
        width: fit-content;
        text-align: left;
        vertical-align: text-top;
        border-radius: .3rem;
        border: 1px solid #bbbbbb;
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        gap: .4rem .8rem;
        align-items: flex-start;
    }

    .input-container {
        display: flex;
        flex-direction: row;
        align-items: flex-end;
    }
    .search-input {
    }
    #search-input {
        border: none;
        display: inline-block;
        outline: none;
        width: 100%;
        position: relative;
    }

    .clear-button {
        border: none;
        background: transparent;
        font-weight: bold;
        font-size: 1rem;
        margin-left: 1rem;
        color: #0b0b0b;
        cursor: pointer;
        text-align: left;
    }

    .input-entry {
        background: #E8E8E8;
        border-radius: .3rem;
        font-size: 1rem;
        color: #0b0b0b;
        padding: .4rem;
    }

    .agents-list {
        display: flex;
        flex-direction: column;
        gap: .8rem;
    }
    .agents-list p {
        text-align: center;
    }

    .agent-result {
        background: #ededed;
        font-weight: bold;
    }
    .agent-result td {
        text-align: left;
        width: fit-content;
        text-wrap: nowrap;
        padding: 1.33rem;
        font-size: 1rem;
        border-block: .85px solid #bbbbbb;
    }
    .agent-result td:not(:last-child):not(:first-child) {
        width: 100%;
    }
    .agent-result td button {
        font-size: 1rem;
        padding: 0 1.33rem;
    }

    #agent-results-table:has(tr) ~ p,
    #assigned-agents-table:has(tr) ~ p,
    #agents-list-container:has(#agent-results-table tr) > h4,
    #agents-list-container:not(:has(#agent-results-table tr)) > .search-list-header
    {
        display: none;
    }
</style>

<div id="atribuir">
    <label for="search-input" style="font-weight: bold; font-size: 1rem">Busque por agentes culturais</label>
    <p>Você pode buscar um agente individualmente ou utilizar vários CPFs e nomes para encontrá-los</p>

    <label for="search-input" class="input-container">
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
            <div style="display: flex; align-items: stretch; max-width: 70%">
                <strong>Utilize o botão para atribuir cotas a todos agentes na lista abaixo.</strong>
                <button class="btn btn-primary">Atribuir cota racial em lote</button>
            </div>
        </div>
        <hr>
        <div class="agents-list">
            <input type="hidden" name="result-ids" value="[]">
            <table id="agent-results-table"></table>
            <table id="assigned-agents-table"><tr><td>Um agente</td></tr></table>
            <p>Ainda não possuem agentes culturais com cotas atribuídas.</p>
            <p><strong>Utilize a busca para encontrar agentes culturais</strong></p>
        </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('search-input');
    const clearButton = document.querySelector('.clear-button');
    const searchValues = {
        element: document.querySelector('[name=search-values]'),
        values: JSON.parse(document.querySelector('[name=search-values]').value),
        push: (value) => {
            searchValues.values.push(value);
            searchValues.element.value = JSON.stringify(searchValues.values);
            findByAllValues(searchValues.values);
        },
        clear: () => {
            searchValues.values = [];
            searchValues.element.value = JSON.stringify(searchValues.values);
            clearRendered();
        },
        pop: () => {
            searchValues.values.pop();
            searchValues.element.value = JSON.stringify(searchValues.values);
            clearRendered();
            if (searchValues.values.length > 0) {
                findByAllValues(searchValues.values);
            }
        },
    };
    const resultIds = {
        element: document.querySelector('[name=result-ids]'),
        values: JSON.parse(document.querySelector('[name=result-ids]').value),
        push: (value) => {
            resultIds.values.push(value);
            resultIds.element.value = JSON.stringify(resultIds.values);
        },
        clear: () => {
            resultIds.values = [];
            resultIds.element.value = JSON.stringify(resultIds.values)
        },
        pop: () => {
            resultIds.values.pop();
            resultIds.element.value = JSON.stringify(resultIds.values);
        },
    };

    clearButton.addEventListener('click', () => {
        $('.input-entry').remove();
        searchInput.value = '';
        searchValues.clear();
    });

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            const inputValue = searchInput.value.trim();
            if (inputValue !== '') {
                searchInput.parentNode.insertBefore($(`<span class="input-entry">${inputValue}</span>`).each2(node => node)[0], searchInput);
                searchInput.value = '';
                searchValues.push(inputValue);
            }
        } else if (event.key === 'Backspace' && searchInput.value === '') {
            $('.input-entry').last().remove();
            searchValues.pop();
        }
    });

    searchInput.addEventListener('paste', event => {
        event.preventDefault();
        const values = (event.clipboardData || window.clipboardData).getData('text').split("\n");
        if (values.length > 1) {
            values.forEach(value => {
                if (value !== '') {
                    searchInput.parentNode.insertBefore($(`<span class="input-entry">${value}</span>`).each2(node => node)[0], searchInput);
                    searchValues.push(value)
                }
            });
        }
    })

    const findAgents = (value) => {
        $.ajax({
            url: `/api/agent/findByCpfOrName?keyword=${value}`,
            success: (response) => {
                let html = '';
                renderResults(response)
            },
            error: (error) => {
                console.error(error);
            }
        });
    }

    const findByAllValues = values => {
        $('#assigned-agents-table tr').remove();
        for (const value of values) {
            findAgents(value);
        }
    }

    const clearRendered = () => {
        $('#agent-results-table tr').remove();
        resultIds.clear();
    }

    const renderResults = (agents) => {
        let html = '';
        for (const agent of agents) {
            // Não inclui na listagem se o agente já foi adicionado
            if (resultIds.values.includes(agent.id)) {
                continue;
            }
            resultIds.push(agent.id)

            html += `<tr class="agent-result">
                        <td>${agent.cpf}</td>
                        <td>${agent.name}</td>
                        <td><button class="btn btn-primary">Atribuir cota racial</button></td>
                    </tr>`;
        }
        $('#agent-results-table').append(html)

        if (searchValues.values.length === 0) {
            clearRendered();
        }
    }

    const showAssignedAgents = (agents) => {
        let html = '';
        for (const agent of agents) {
            html += `<tr>
                <td>${agent.cpf}</td>
                <td>${agent.name}</td>
            </tr>`;
        }
        $('#assigned-agents-table').append(html)
    }
</script>
