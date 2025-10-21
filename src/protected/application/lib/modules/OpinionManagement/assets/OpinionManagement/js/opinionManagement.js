const handleChkCollapseChange = (target, evaluationMethod) => {
    [...target.parentNode.querySelectorAll('.collapsible')].forEach(el => el.classList.toggle('collapsible-hidden'));
    if (evaluationMethod === 'technical') {
        return;
    }

    const chkCollapses = $('.chk-collapse');
    for (let i= 0; i < chkCollapses.length; i++) {
        if (chkCollapses[i] === target) {
            continue;
        }
        chkCollapses[i].checked = false;
    }
}

const resultHtml = (opinionUrl, result, evaluationMethod) => {
    if (evaluationMethod === 'documentary') {
        return `<p>
            Resultado da avaliação documental:
            <a href="${opinionUrl}"
               class="criteria-status-${result < 0 ? 'invalid' : 'valid'}"></a>
        </p>`;
    } else if (evaluationMethod === 'technical') {
        return `<p>Nota da avaliação técnica: <a href="${opinionUrl}">${result}</a></p>`;
    }

    return '';
}

const evaluationToHtml = (opinion, evaluationMethod, criteria = null) => {
    let evaluationHtml = '';
    if (evaluationMethod === 'documentary') {
        for (const criteriaId in opinion.evaluationData) {
            const criteria = opinion.evaluationData[criteriaId];
            criteria.obs_items = criteria.obs_items?.replace('\n','<br>');
            criteria.obs = criteria.obs?.replace('\n','<br>');

            evaluationHtml += `<div class="criteria-fields">
                <h5>${criteria.label}</h5>
                <p class="criteria-status-${criteria.evaluation === '' ? 'pending' : criteria.evaluation}"></p>
                ${criteria.evaluation === 'invalid' ? `<p class="opinion-evaluation-obs">${criteria['obs_items']}</p>` : ''}
                <p class="opinion-evaluation-obs">${criteria['obs']}</p>
            </div>`;
        }
    } else if (evaluationMethod === 'technical') {
        let maxScore = 0;
        evaluationHtml += `<div class="criteria-fields">
            <h5>Pontuações:</h5>`;

        for (const criteriaId in criteria) {
            const [ title ] = criteria[criteriaId].title.split(':');
            evaluationHtml += `<p>${title}: <strong>${opinion.evaluationData[criteriaId]}</strong></p>`;
            maxScore += parseFloat(criteria[criteriaId].max);
        }

        evaluationHtml += `<p>Pontuação total do inscrito: <strong>${opinion.result}</strong></p>
            <p>Pontuação máxima: <strong>${maxScore}</strong></p>
            <br/>
            <h5>Parecer técnico</h5>
            <p class="opinion-evaluation-obs">${opinion.evaluationData.obs}</p>
        </div>`;
    }

    return evaluationHtml;
}

const opinionHtml = (opinion, evaluationMethod, criteria = null) => {
    return `<div class="opinion">
        <div class="evaluation-title">
            <h3>
                Parecerista
                ${opinion.agent.singleUrl
                    ? '<a href="'+opinion.agent.singleUrl+'" target="_blank">'+opinion.agent.name+'</a>'
                    : opinion.agent.name}
            </h3>
            ${opinion.result === null
                ? '<div>Avaliação <span class="criteria-status-pending"></span></div>'
                : `<label for="chk-collapse-${opinion.id}">
                    <div class="collapsible">Exibir
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="6.293 8.293 11.414 7.414">
                            <path fill="currentColor" fill-rule="evenodd" d="M7 9a1 1 0 0 0-.707 1.707l5 5a1 1 0 0 0 1.414 0l5-5A1 1 0 0 0 17 9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="collapsible collapsible-hidden">Esconder
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="6.293 8.293 11.414 7.414">
                            <g transform="rotate(180 12 12)">
                                <path fill="currentColor" fill-rule="evenodd" d="M7 9a1 1 0 0 0-.707 1.707l5 5a1 1 0 0 0 1.414 0l5-5A1 1 0 0 0 17 9z" clip-rule="evenodd"/>
                            </g>
                        </svg>
                    </div>
                </label>`
                + resultHtml(opinion.singleUrl, opinion.result, evaluationMethod)}
        </div>
        <input
            type="checkbox"
            id="chk-collapse-${opinion.id}"
            class="chk-collapse"
            name="chk-collapse"
            onchange="handleChkCollapseChange(this, '${evaluationMethod}')"
        />
        ${evaluationToHtml(opinion, evaluationMethod, criteria)}
    </div>`;
}

const showOpinions = registrationId => {
    fetch(MapasCulturais.baseURL + 'opinionManagement/opinions?'+ new URLSearchParams({
        id: registrationId
    }), {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (response.redirected) {
                throw new Error('Guest');
            }
            if (!response.ok) {
                throw new Error(response.statusText);
            }
            return response.json();
        })
        .then(({opinions, evaluationMethod, criteria = null, registration = null, appliedAffirmativePolicy = false}) => {
            // Caso não tenham avaliadores atribuídos mostrará este alerta
            if (opinions.length === 0) {
                return Swal.fire({
                    title: "Não há avaliações!",
                    text: "As avaliações desta inscrição ainda não foram iniciadas."
                });
            }

            const html = `<div>${consolidatedResultHtml(registration, evaluationMethod, appliedAffirmativePolicy)
                + opinions.map(opinion => opinionHtml(opinion, evaluationMethod, criteria)).join('')}</div>`;

            Swal.fire({
                html,
                showCloseButton: true,
                showConfirmButton: false,
            });
        })
        .catch(error => {
            let { message } = error;
            if (error.message === 'Forbidden') {
                message = 'Você não tem permissão para acessar este recurso.';
            } else if (error.message === 'Guest') {
                message = 'É necessário estar autenticado.';
            }else if (error.message === `Not Found`) {
                message = 'Os pareceres ainda não foram enviados.';
            }

            errorAlert(message, error.message);
        });
}

const publishOpinions = target => {
    const opportunityId = target.getAttribute('data-id');

    Swal.fire({
        title: "Essa ação é irreversível!",
        html: "Ao clicar em <strong>'Publicar'</strong> você está publicando os pareceres para a visualização dos proponentes. Isso não pode ser desfeito.",
        showConfirmButton: true,
        showCloseButton: false,
        showCancelButton: true,
        confirmButtonText: 'Publicar',
        cancelButtonText: 'Cancelar',
    })
        .then(result => {
            if (result.isConfirmed) {
                fetch(MapasCulturais.baseURL + 'opinionManagement/publishOpinions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        id: opportunityId,
                    }),
                })
                    .then(response => {
                        if (response.redirected) {
                            throw new Error('Guest');
                        }
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .then(response => {
                        Swal.fire({
                            title: "Pareceres publicados com sucesso!",
                            text: "Os pareceres desta inscrição agora encontram-se publicados.",
                            showConfirmButton: false,
                            showCloseButton: true,
                        });
                    })
                    .catch(error => {
                        let { message } = error;
                        if (error.message === 'Forbidden') {
                            message = 'Você não tem permissão para acessar este recurso.';
                        } else if (error.message === 'Guest') {
                            message = 'É necessário estar autenticado.';
                        }

                        errorAlert(message);
                    });
            }
        });
}

const errorAlert = (message, errorType = '') => {
    if (errorType === 'Not Found') {
        Swal.fire({
            title: "Oops...",
            text: message,
            showConfirmButton: false,
            showCloseButton: true,
        });
    } else {
        Swal.fire({
            title: "Oops...",
            text: "Aconteceu um problema!",
            footer: `<code style="font-size:11px; color:#c93">${message}</code>`,
            showConfirmButton: false,
            showCloseButton: true,
        });
    }
}

const consolidatedResultHtml = (registration, evaluationMethod, appliedAffirmativePolicy) => {
    if (evaluationMethod === 'technical' && appliedAffirmativePolicy) {
        let html = `<div class="opinion">
            <div class="evaluation-title" style="display: block">
                <p>Média das avaliações: <strong>${appliedAffirmativePolicy.raw}</strong></p>
                <br>`;

        if (appliedAffirmativePolicy.rules.length > 0) {
            html += `<p>Políticas afirmativas aplicadas:</p>
                <ul>
                    ${appliedAffirmativePolicy.rules
                        .map(policy => `<li>${policy.field.title} &#10141; <strong>${policy.value}</strong> (+${policy.percentage} %)</li>`)
                        .join('')}
                </ul>`;
        }

        return html + `<p>Nota final consolidada: <strong>${registration.evaluationResultValue}</strong></p>
            </div>
        </div>`;
    }
    return '';
}

// Função de alerta sobrescrita substituindo diálogo padrão do navegador por uma do SweetAlert
alertPublish = id => {
    Swal.fire({
        title: "Tem certeza?",
        html: "<strong>ATENÇÃO</strong>, essa ação é uma ação irreversível. Caso a próxima fase seja uma prestação de contas, primeiro crie a fase de prestação de contas para só depois fazer a publicação.",
        showConfirmButton: true,
        showCloseButton: false,
        showCancelButton: true,
        confirmButtonText: 'Publicar',
        cancelButtonText: 'Cancelar',
    })
        .then(result => {
            if (!result.isConfirmed) {
                MapasCulturais.Messages.alert("Ação cancelada!");
                return;
            }

            let loading = Swal.fire({
                title: "Verificando se há notificações a serem enviadas.",
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            let url = MapasCulturais.createUrl('opportunity', 'publishRegistrations', [id]);
            $.get(url, function() {
                loading.close();
                MapasCulturais.Messages.success('Resultado publicado');
            });
            location.reload();
        });
}