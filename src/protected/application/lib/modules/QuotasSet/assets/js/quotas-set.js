const quotas = {
    types: [],
    getTypes() {
        return this.types
    },
    setTypes(types) {
        this.types = types
    },
}

document.addEventListener('DOMContentLoaded', function () {
    Object.defineProperty(String.prototype, 'capitalize', {
        value: function() {
            return this.charAt(0).toUpperCase() + this.slice(1);
        },
        enumerable: false
    });

    getQuotaTypes()

    const findAgents = (value) => {
        $.ajax({
            url: `/api/agent/findByCpfOrName?keyword=${value}`,
            success: (response) => {
                renderResults(response);
            },
            error: (error) => {
                if (typeof MapasCulturais !== 'undefined' && MapasCulturais.Messages) {
                    MapasCulturais.Messages.error('Sem agentes encontrados.');
                }
            }
        });
    };

    const findByAllValues = (values) => {
        if (!assignedAgentsTable) return;
        assignedAgentsTable.clear().draw();
        for (const value of values) {
            findAgents(value);
        }
    };

    const clearRendered = () => {
        if (agentResultsTable) agentResultsTable.clear().draw();
        if (assignedAgentsTable) assignedAgentsTable.clear().draw();
        resultIds.clear();
        findAssignedAgents();
    };

    const renderResults = (agents) => {
        if (!agentResultsTable) return;
        let data = [];
        for (const agent of agents) {
            if (resultIds.values.includes(agent.id)) {
                continue;
            }
            resultIds.push(agent.id);
            let buttonHtml = `<button class="btn btn-primary" single-assign-btn agent-id="${agent.id}">Atribuir cota</button>`;
            data.push([agent.cpf, agent.name, buttonHtml]);
        }
        agentResultsTable.rows.add(data).draw();
    };

    const findAssignedAgents = () => {
        $.ajax({
            url: `/api/agent/allWithQuotas`,
            success: (response) => {
                renderAssignedAgents(response);
            }
        });
    };

    const renderAssignedAgents = (agents) => {
        if (!assignedAgentsTable) return;
        assignedAgentsTable.clear();
        let data = [];
        for (const agent of agents) {
            let hasRacial = false;
            let quotasHtml = agent.quotas_policy.map(quota => {
                hasRacial = quota.quotas_policy.name.includes('racial') || quota.quotas_policy.name.includes('Racial');
                return `<div>
                    <strong>${quota.quotas_policy.name.replace('Cota ', '').capitalize()}</strong>
                </div>`;
            }).join('');
            let periodHtml = agent.quotas_policy.map(quota => {
                return `<div>
                    <span>até ${new Date(quota.end_date).toLocaleDateString('pt-br', {year: 'numeric', month: 'short', day: 'numeric'})}</span>
                </div>`;
            }).join('');
            let buttonHtml = `<button class="btn btn-default" onclick="unassignQuota(${agent.quotas_policy[0].id}, 1, ${agent.id}, this)">Remover cota</button>`
            data.push([agent.cpf, agent.name, quotasHtml, periodHtml, buttonHtml]);
        }
        assignedAgentsTable.rows.add(data).draw();
    };

    const assignQuota = (quotaId, agentId, target, typeAssig = 'single') => {
        if (!quotaId) {
            Swal.fire({
                icon: "error",
                title: "Cota não atribuída",
                text: "Selecione uma cota para atribuir.",
            })
            return
        }
        const startDate = $('#quota-start-date').val()

        $.ajax({
            url: `/api/agent/assignQuota`,
            method: 'POST',
            data: JSON.stringify({
                agent_id: agentId,
                quota_id: quotaId,
                start_date: startDate ? startDate : new Date().toISOString().split('T')[0]
            }),
            contentType: "application/json",
            success: (response) => {
                target.classList.remove('btn-primary');
                target.classList.add('btn-default');
                target.innerText = 'Remover cota';
                target.onclick = () => unassignQuota(response.id, quotaId, agentId, target);

                const row = $(target).closest('tr');
                if (agentResultsTable) {
                    agentResultsTable.row(row).remove().draw();
                }

                findAssignedAgents();
            },
            error: (error) => {
                if (typeof MapasCulturais !== 'undefined' && MapasCulturais.Messages) {
                    MapasCulturais.Messages.error('Erro inesperado ao associar cota.');
                }
            }
        });

        if (typeAssig === 'batch') searchValues.clear()
    };

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
                target.innerText = 'Atribuir cota';
                target.onclick = () => assignQuota(quotaId, agentId, target);
                clearRendered();
            },
            error: (error) => {
                if (typeof MapasCulturais !== 'undefined' && MapasCulturais.Messages) {
                    MapasCulturais.Messages.error('Erro inesperado ao desassociar.');
                }
            }
        });
    };

    const searchInput = document.getElementById('search-input');
    const clearButton = document.querySelector('.clear-button');
    const searchValuesElement = document.querySelector('[name=search-values]');
    const resultIdsElement = document.querySelector('[name=result-ids]');

    const searchValues = {
        element: searchValuesElement,
        values: searchValuesElement ? JSON.parse(searchValuesElement.value || '[]') : [],
        push: (value) => {
            if (searchValuesElement) {
                searchValues.values.push(value);
                searchValuesElement.value = JSON.stringify(searchValues.values);
                findByAllValues(searchValues.values);
                $('#assigned-agents-table_wrapper').addClass('has-search');
                $('#agent-results-table_wrapper').addClass('has-results');
                $('.search-list-header').addClass('has-search');
                $('#results-export-buttons').addClass('has-results');
                $('#assigned-export-buttons').addClass('has-search');
            }
        },
        clear: () => {
            searchValues.values = [];
            if (searchValuesElement) {
                searchValuesElement.value = JSON.stringify(searchValues.values);
            }
            clearRendered();
            $('#assigned-agents-table_wrapper').removeClass('has-search');
            $('#agent-results-table_wrapper').removeClass('has-results');
            $('.search-list-header').removeClass('has-search');
            $('#results-export-buttons').removeClass('has-results');
            $('#assigned-export-buttons').removeClass('has-search');
        },
        pop: () => {
            searchValues.values.pop();
            if (searchValuesElement) {
                searchValuesElement.value = JSON.stringify(searchValues.values);
            }
            clearRendered();
            if (searchValues.values.length > 0) {
                findByAllValues(searchValues.values);
            } else {
                $('#assigned-agents-table_wrapper').removeClass('has-search');
                $('#agent-results-table_wrapper').removeClass('has-results');
                $('.search-list-header').removeClass('has-search');
                $('#results-export-buttons').removeClass('has-results');
                $('#assigned-export-buttons').removeClass('has-search');
            }
        },
    };

    const resultIds = {
        element: resultIdsElement,
        values: resultIdsElement ? JSON.parse(resultIdsElement.value || '[]') : [],
        push: (value) => {
            resultIds.values.push(value);
            if (resultIdsElement) {
                resultIdsElement.value = JSON.stringify(resultIds.values);
            }
        },
        clear: () => {
            resultIds.values = [];
            if (resultIdsElement) {
                resultIdsElement.value = JSON.stringify(resultIds.values);
            }
        },
        pop: () => {
            resultIds.values.pop();
            if (resultIdsElement) {
                resultIdsElement.value = JSON.stringify(resultIds.values);
            }
        },
    };

    let agentResultsTable;
    let assignedAgentsTable;

    $(document).ready(function() {
        if (!$.fn.DataTable) {
            return;
        }

        agentResultsTable = $('#agent-results-table').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            dom: '<"top"lfi>rt<"bottom"ip>',
            lengthMenu: [ [10, 25, 50, 100], [10, 25, 50, 100] ], 
            buttons: [
                {
                    extend: 'csv',
                    text: 'Exportar CSV',
                    exportOptions: {
                        columns: [0, 1],
                        format: {
                            body: function (data, row, column, node) {
                                return data.replace(/<[^>]*>/g, '');
                            }
                        }
                    }
                }
            ],
            language: {
                emptyTable: "Nenhum agente encontrado",
                paginate: {
                    previous: "Anterior",
                    next: "Próximo"
                },
                info: "Exibindo _START_ a _END_ de _TOTAL_ entradas",
                lengthMenu: "Mostrar _MENU_ entradas",
                search: "",
                searchPlaceholder: "Buscar..." 
            },
            initComplete: function() {
                this.api().buttons().container().hide();
            }
        });

        assignedAgentsTable = $('#assigned-agents-table').DataTable({
            paging: true,
            searching: true, 
            ordering: true,
            info: true,
            dom: '<"top"lfi>rt<"bottom"ip>',
            lengthMenu: [ [10, 25, 50, 100], [10, 25, 50, 100] ],
            buttons: [
                {
                    extend: 'csv',
                    text: 'Exportar CSV',
                    exportOptions: {
                        customizeData: function(csv) {
                            csv.header = ["CPF", "Nome", "Cotas", "Período"];
                            csv.body.forEach(function(row) {
                                let quotasCell = row[2];
                                let periodCell = row[3];
                                row[2] = quotasCell.replace(/<[^>]*>/g, '').replace('Cotas:', '').trim() || '';
                                row[3] = periodCell.replace(/<[^>]*>/g, '').trim() || '';
                                row.length = 4;
                            });
                        }
                    }
                }
            ],
            language: {
                emptyTable: "Nenhum agente encontrado",
                paginate: {
                    previous: "Anterior",
                    next: "Próximo"
                },
                info: "Exibindo _START_ a _END_ de _TOTAL_ entradas",
                lengthMenu: "Mostrar _MENU_ entradas",
                search: "",
                searchPlaceholder: "Buscar..." 
            },
            initComplete: function() {
                this.api().buttons().container().hide();
            }
        });

        $('#export-results-csv').on('click', function() {
            if (agentResultsTable) {
                agentResultsTable.button(0).trigger();
            }
        });

        $('#export-assigned-csv').on('click', function() {
            if (assignedAgentsTable) {
                assignedAgentsTable.button(0).trigger();
            }
        });

        findAssignedAgents();
    });

    $('#bulk-assign-button').on('click', function () {
        Swal.fire(swalConfigAssignQuota()).then(res => {
            if (res.isConfirmed) {
                const quotaType = $('#type-quota').val()

                $('#agent-results-table [single-assign-btn]').each(function () {
                    const agentId = this.attributes['agent-id'].value

                    assignQuota(quotaType, agentId, this, 'batch')
                })
            }
        })
    })

    $('#agent-results-table').on('click', '[single-assign-btn]', function () {
        Swal.fire(swalConfigAssignQuota()).then(res => {
            if (res.isConfirmed) {
                const quotaType = $('#type-quota').val()
                const agentId = this.attributes['agent-id'].value

                assignQuota(quotaType, agentId, this)
            }
        })
    })

    if (clearButton) {
        clearButton.addEventListener('click', () => {
            $('.input-entry').remove();
            if (searchInput) searchInput.value = '';
            searchValues.clear();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                const inputValue = searchInput.value.trim();
                if (inputValue !== '') {
                    searchInput.parentNode.insertBefore($(`<span class="input-entry">${inputValue}</span>`).get(0), searchInput);
                    searchInput.value = '';
                    searchValues.push(inputValue);
                }
            } else if (event.key === 'Backspace' && searchInput.value === '') {
                $('.input-entry').last().remove();
                searchValues.pop();
            }
        });

        searchInput.addEventListener('paste', event => {
            const values = (event.clipboardData || window.clipboardData).getData('text').split("\n");
            if (values.length > 1) {
                event.preventDefault();
                values.forEach(value => {
                    if (value !== '') {
                        searchInput.parentNode.insertBefore($(`<span class="input-entry">${value}</span>`).get(0), searchInput);
                        searchValues.push(value);
                    }
                });
            }
        });
    }

    window.assignQuota = assignQuota;
    window.unassignQuota = unassignQuota;
});

const getQuotaTypes = () => {
    $.ajax({
        url: `/api/agent/quotaTypes`,
        success(res) {
            quotas.setTypes(res)
        }
    })
}

const swalConfigAssignQuota = () => {
    return {
        title: "Atribuir cota",
        html: htmlAssignQuota(),
        showCancelButton: true,
        confirmButtonText: 'Atribuir',
        cancelButtonText: 'Cancelar',
    }
}

const htmlAssignQuota = () => {
    return `
        <p style="margin-bottom: 20px;">Selecione qual cota deseja atribuir e a data de início da validade.</p>
        <div class="form-group">
            <label class="sweetalert-label">Cota:</label>
            <select id="type-quota" class="form-control">
                <option disabled selected>-- Selecione a cota --</option>
                ${quotas.getTypes().map(type => `<option value="${type.id}">${type.name}</option>`).join('')}
            </select>
        </div>
        <div class="form-group">
            <label class="sweetalert-label">Data de Início:</label>
            <input type="date" id="quota-start-date" class="form-control">
        </div>
    `
}
