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
        vertical-align: middle;
    }
    #agent-results-table td:nth-child(2),
    #assigned-agents-table td:nth-child(3) {
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

    #bulk-assign-button {
        height: 3rem;
        width: 100%;
        display: inline-block;
        font-size: 1rem;
        margin: 0;
    }

    .assigned-quotas-details {
        display: flex;
        gap: 1rem;
        font-weight: normal;
    }
    .assigned-quotas-details>* {
        display: inline-block;
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
                <strong>Utilize o botão para atribuir cotas a todos os agentes na lista abaixo.</strong>
                <button class="btn btn-primary" id="bulk-assign-button" onclick="bulkAssignQuota(this)">Atribuir cota racial em lote</button>
            </div>
        </div>
        <hr>
        <div class="agents-list">
            <input type="hidden" name="result-ids" value="[]">
            <table id="agent-results-table"></table>
            <table id="assigned-agents-table"></table>
            <p>Ainda não possuem agentes culturais com cotas atribuídas.</p>
            <p><strong>Utilize a busca para encontrar agentes culturais</strong></p>
        </div>
    </div>
</div>

<script>
    Object.defineProperty(String.prototype, 'capitalize', {
        value: function() {
            return this.charAt(0).toUpperCase() + this.slice(1);
        },
        enumerable: false
    });

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
        $('.agents-list tr').remove();
        resultIds.clear();
        findAssignedAgents();
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
                        <td>`;
            if (agent.assigned) {
                html += `<button class="btn btn-default" onclick="unassignQuota(${agent.assigned_id}, 9, ${agent.id}, this)">Remover cota</button>`;
            } else {
                html += `<button class="btn btn-primary" onclick="assignQuota(9, ${agent.id}, this)">Atribuir cota racial</button>`;
            }
            html += `</td>
            </tr>`;
        }
        $('#agent-results-table').append(html)

        if (searchValues.values.length === 0) {
            clearRendered();
        }
    }

    const findAssignedAgents = () => {
        $.ajax({
            url: `/api/agent/allWithQuotas`,
            success: (response) => {
                renderAssignedAgents(response)
            },
            error: (error) => {
                console.error(error);
            }
        });
    }
    const renderAssignedAgents = (agents) => {
        let html = '';
        for (const agent of agents) {
            let hasRacial = false;
            html += `<tr class="agent-result">
                <td>${agent.cpf}</td>
                <td>${agent.name}</td>
                <td>
                    <div class="assigned-quotas-details">
                        <div>Cotas:</div>
                        <div>${agent.quotas_policy.map(quota => {
                            hasRacial = quota.quotas_policy.name.includes('racial') || quota.quotas_policy.name.includes('Racial');
                            return `<div>
                                <strong>${quota.quotas_policy.name.replace('Cota ', '').capitalize()} (</strong><span>até ${new Date(quota.end_date)
                                    .toLocaleDateString('pt-br', {year: 'numeric', month: 'short', day: 'numeric'})
                                }</span><strong>);</strong>
                            </div>`;
                        }).join('')}</div>
                </td>
                <td>`;
            html += hasRacial ? `<button class="btn btn-default" onclick="unassignQuota(${agent.quotas_policy[0].id}, 9, ${agent.id}, this)">Remover cota</button>` : '';
            html += `</td>
            </tr>`;
        }
        $('#assigned-agents-table').append(html)
    }

    const assignQuota = (quotaId, agentId, target) => {
        $.ajax({
            url: `/api/agent/assignQuota`,
            method: 'POST',
            data: JSON.stringify({
                agent_id: agentId,
                quota_id: quotaId,
                start_date: new Date().toISOString().split('T')[0]
            }),
            contentType: "application/json",
            success: (response) => {
                target.classList.remove('btn-primary');
                target.classList.add('btn-default');
                target.innerText = 'Remover cota';
                target.onclick = () => unassignQuota(response.id, quotaId, agentId, target);
            },
            error: (error) => {
                console.error(error);
            }
        });
    }

    const bulkAssignQuota = (target) => {
        $('#agent-results-table .agent-result button[onclick^="assign"]').click();
        target.parentNode.innerHTML = '<div class="alert success" style="font-size: 1rem">A cota foi atribuída aos agentes culturais!</div>';
    }

    const unassignQuota = (agentQuotaId, quotaId, agentId, target) => {
        $.ajax({
            url: `/api/agent/unassignQuota`,
            method: 'POST',
            data: JSON.stringify({
                agent_quota_id: agentQuotaId
            }),
            contentType: "application/json",
            success: (response) => {
                target.classList.remove('btn-default');
                target.classList.add('btn-primary');
                target.innerText = 'Atribuir cota racial';
                target.onclick = () => assignQuota(quotaId, agentId, target);
            },
            error: (error) => {
                console.error(error);
            }
        });
    }

    document.body.onload = findAssignedAgents;
</script>
