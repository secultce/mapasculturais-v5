$(document).ready(() => {
    $('#draw-button').on('click', async e => {
        e.preventDefault();

        const confirmWindow = await Swal.fire({
            title: 'Confirmar o sorteio?',
            showDenyButton: true,
            denyButtonText: 'Não, sortear depois',
            confirmButtonText: 'Sim',
            reverseButtons: true,
            customClass: {
                actions: 'space-between',
            },
        })
        if(!confirmWindow.isConfirmed)
            return;

        $('#draw-button').hide();
        $('#draw-loading').show();

        const url = MapasCulturais.createUrl('sorteio-inscricoes', 'draw', [MapasCulturais.entity.id]);
        const category = $('#categories-draw').val();

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({category}).toString(),
        })
            .then(response => {
                $('#draw-button').show();
                $('#draw-loading').hide();

                if (!response.ok) {
                    let err = new Error("HTTP status code: " + response.status);
                    err.response = response;
                    err.status = response.status;
                    throw err;
                }
                return response.json();
            })
            .then(data => {
                renderRanking(data, category);
            })
            .catch(error => {
                if(error.status === 400) {
                    MapasCulturais.Messages.alert('Não existem novos inscritos aprovados nessa categoria');
                    return;
                }
                console.error(error);
                MapasCulturais.Messages.error('Ocorreu um erro ao gerar ranking');
                MapasCulturais.Messages.alert('Atualize a página e tente novamente!');
            });
    });

    $('#history-categories-filter').on('change', e => {
        const category = e.target.value;
        const categoryLists = $('.category-list');

        if (category === '') {
            categoryLists.each((_, elem) => {
                elem.style.display = 'flex';
            });
            return;
        }

        categoryLists.each((_, elem) => {
            elem.style.display = 'none';
        });
        document.querySelector(`.category-list[data-category="${category}"]`).style.display = 'flex';
    });

    $('#download-pdf-ranking').on('click', e => {
        e.preventDefault();
        downloadFile('download-pdf-ranking', 'pdf');
    });
    $('#download-xlsx-ranking').on('click', e => {
        e.preventDefault();
        downloadFile('download-xlsx-ranking', 'xlsx');
    });

    $('#download-pdf-category-ranking').on('click', e => {
        e.preventDefault();
        const category = document.getElementById('current-category-name').innerText;
        downloadFile('download-pdf-category-ranking', 'pdf', category);
    });
    $('#download-xlsx-category-ranking').on('click', e => {
        e.preventDefault();
        const category = document.getElementById('current-category-name').innerText;
        downloadFile('download-xlsx-category-ranking', 'xlsx', category);
    });

    $('#publish-ranking').on('click', e => {
        e.preventDefault();

        const url = MapasCulturais.createUrl(
            'sorteio-inscricoes',
            'publish',
            [MapasCulturais.entity.id]
        );

        fetch(url, {
            method: 'POST',
        })
            .then(response => {
                if(response.status !== 204)
                    throw new Error(response.status);

                const btnPublish = $('#publish-ranking');
                btnPublish.after($('<button class="btn published-draw-label">&#9989; Sorteios publicados</button>'));
                btnPublish.remove();
            })
            .catch(err => console.error(err))
    });

    document.querySelector('label[data-draw-id]').click();

    const renderRanking = (draw, category) => {
        const { drawRegistrations } = draw;
        const tableBodyElement = $('table#ranking-table tbody');
        tableBodyElement.children()
            .each((_, elem) =>
                elem.style.display = 'none');
        $('#categories-draw').children()
            .each((_, elem) =>
                elem.innerText === category ? elem.setAttribute('disabled', '') : '');

        if(category !== '') {
            $('#drawed-categories-filter').append(`<option selected>${category}</option>`);
        }

        // Add new item on history and set as .active
        const categoryContainerTitle = document.querySelector(`div[data-category="${category}"] > span.history-category`);
        const newItemOnHistory = document.createElement('label');
        newItemOnHistory.classList.add('active');
        newItemOnHistory.setAttribute('data-draw-id', draw.id);
        newItemOnHistory.setAttribute('data-category-name', category);
        newItemOnHistory.setAttribute('onclick', 'filterTableRows(this)');
        categoryContainerTitle.after(newItemOnHistory);

        newItemOnHistory.innerHTML = `Sorteio realizado em: <br>
            <strong>${formatDateTime(draw.createTimestamp.date)}<strong>`;
        // Remove active class from all labels
        $('label[data-draw-id]').each((_, elem) => {
            elem.classList.remove('active');
        });
        // Set current category name to table title
        document.getElementById('current-category-name').innerText = category;

        drawRegistrations.sort((current, next) => {
            return current.rank - next.rank;
        })
        let i = 0;
        const arrayRankingCount = drawRegistrations.length;

        const intervalId = setInterval(() => {
            // Verifica se o índice iguala ou excede o tamanho do array e para de executar a função
            if(i >= arrayRankingCount) {
                clearInterval(intervalId);

                const tr = $('<tr><td colspan="4">&#9989; Sorteio Finalizado</td></tr>')
                tableBodyElement.append(tr);
                return;
            }

            const rank = drawRegistrations[i];
            i++;
            const tr = $(`<tr data-draw-id="${draw.id}" class="approved"></tr>`)
                .append($(`<td>#${rank.rank}</td>` +
                    `<td><a href="${rank.registration.singleUrl}">${rank.registration.number}</a></td>` +
                    `<td><a href="/agente/${rank.registration.owner.id}">${rank.registration.owner.name}</a></td>` +
                    `<td>Selecionado</td>`));

            tableBodyElement.append(tr);
            document.getElementById('ranking-table').scrollIntoView();
        }, 200)
    };

    const formatDateTime = datetime => {
        let [ date, time ] = datetime.split('.')[0].split(' ');
        date = date.split('-').reverse().join('/');
        return `${date} às ${time}`;
    }
});

const filterTableRows = target => {
    const tableBodyElement = $('table#ranking-table tbody');
    const drawId = target.getAttribute('data-draw-id');

    // Remove active class from all labels
    $('label[data-draw-id]').each((_, elem) => {
        elem.classList.remove('active');
    });
    target.classList.add('active');

    // Filter table rows from draw-id in the active label
    tableBodyElement.children()
        .each((_, elem) =>
            elem.getAttribute('data-draw-id') !== drawId && drawId !== ''
                ? elem.style.display = 'none'
                : elem.style.display = 'table-row');

    const currentCategoryName = document.getElementById('current-category-name');
    currentCategoryName.innerText = target.getAttribute('data-category-name');
}

const downloadFile = (buttonId, fileType = 'pdf', category = null) => {
    let filename = '';
    let args = { id: MapasCulturais.entity.id };
    const action = 'download' + fileType;
    if (category) {
        args.category = category;
    }
    const url = '/' + MapasCulturais.createUrl('sorteio-inscricoes', action, args)
        .split(MapasCulturais.baseURL)[1];

    fetch(url)
        .then(response => {
            if(response.status !== 200) {
                throw new Error(response.message);
            }
            const header = response.headers.get('Content-Disposition');
            const parts = header.split(';');
            filename = parts[1].split('=')[1].replaceAll("\"", "");

            return response.blob();
        })
        .then(blob => {
            if (blob == null || blob.size === 0) {
                throw new Error('Arquivo vazio');
            }

            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();

            MapasCulturais.Messages.success('Arquivo baixado com sucesso!');
            MapasCulturais.Messages.help('Verifique seus downloads.');
        })
        .catch(e => {
            console.error(e);
            MapasCulturais.Messages.alert('Ocorreu um erro ao baixar o arquivo');
            setTimeout(() => {
                MapasCulturais.Messages.help('Atualize a página e tente novamente');
            }, 1000);
        });

    $('#'+buttonId).addClass('loading-button');

    setTimeout(() => {
        $('#'+buttonId).removeClass('loading-button');
    }, 4000)
}
