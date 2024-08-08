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
                renderRanking(data.drawRegistrations, category);
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

    $('#drawed-categories-filter').on('change', );

    $('#download-ranking').on('click', e => {
        e.preventDefault();

        const category = $('#drawed-categories-filter').val();

        window.location = MapasCulturais.createUrl('sorteio-inscricoes', 'downloadcsv', {
            id: MapasCulturais.entity.id,
            category,
        });

        console.log(e.target)

        $('#download-ranking').addClass('loading-button');

        setTimeout(() => {
            $('#download-ranking').removeClass('loading-button');
        }, 4000)
    });

    $('#pusblish-ranking').on('click', e => {
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

                const btnPublish = $('#pusblish-ranking');
                btnPublish.after($('<button class="btn published-draw-label">&#9989; Sorteios publicados</button>'));
                btnPublish.remove();
            })
            .catch(err => console.error(err))
    });

    const renderRanking = ((arrayRanking, category) => {
        const tableBodyElement = $('table#ranking-table tbody');
        tableBodyElement.children().each((key, elem) => elem.style.display = 'none');
        $('#categories-draw').children()
            .each((key, elem) =>
                elem.innerText === category ? elem.setAttribute('disabled', '') : '');

        if(category !== '') {
            $('#drawed-categories-filter').append(`<option selected>${category}</option>`);
        }

        arrayRanking.sort((current, next) => {
            return current.rank - next.rank;
        })
        let i = 0;
        const arrayRankingCount = arrayRanking.length;

        const intervalId = setInterval(() => {
            // Verifica se o índice iguala ou excede o tamanho do array e para de executar a função
            if(i >= arrayRankingCount) {
                clearInterval(intervalId);

                const tr = $('<tr><td colspan="4">&#9989; Sorteio Finalizado</td></tr>')
                tableBodyElement.append(tr);
                return;
            }

            const rank = arrayRanking[i];
            i++;
            const tr = $(`<tr data-category="${category}" class="approved"></tr>`)
                .append($(`<td><a href="/inscricao/${rank.registration.id}">${rank.registration.number}</a></td>` +
                    `<td>${rank.registration.category}</td>` +
                    `<td><a href="/agente/${rank.registration.owner.id}">${rank.registration.owner.name}</a></td>` +
                    `<td>#${rank.rank}</td>`));

            tableBodyElement.append(tr);
            document.getElementById('ranking-table').scrollIntoView();
        }, 200)
    });
});

const filterTableRows = e => {
    const tableBodyElement = $('table#ranking-table tbody');
    const drawId = e.target.getAttribute('data-draw-id');

    tableBodyElement.children()
        .each((key, elem) =>
            elem.getAttribute('data-draw-id') !== drawId && drawId !== ''
                ? elem.style.display = 'none'
                : elem.style.display = 'table-row');
}
