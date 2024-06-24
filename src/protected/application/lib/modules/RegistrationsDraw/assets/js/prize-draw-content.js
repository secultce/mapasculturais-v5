$(document).ready(() => {
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
            .then(response => {
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
                    MapasCulturais.Messages.alert('Essa categoria já foi sorteada.');
                    return;
                }
                console.error(error);
                MapasCulturais.Messages.error('Ocorreu um erro ao gerar ranking');
                MapasCulturais.Messages.alert('Atualize a página e tente novamente!');
            });
    });

    const renderRanking = ((arrayRanking, category) => {
        const tableBodyElement = $('table#ranking-table tbody');
        tableBodyElement.children().each((key, elem) => elem.style.display = 'none');
        $('#categories-draw').children()
            .each((key, elem) =>
                elem.innerText === category ? elem.setAttribute('disabled', '') : '');
        $('#drawed-categories-filter').append(`<option selected>${category}</option>`);

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
            const tr = $('<tr class="approved"></tr>')
                .append($(`<td><a href="/inscricao/${rank.registration.id}">${rank.registration.number}</a></td>` +
                    `<td>${rank.registration.category}</td>` +
                    `<td><a href="/agente/${rank.registration.owner.id}">${rank.registration.owner.name}</a></td>` +
                    `<td>#${rank.rank}</td>`));

            tableBodyElement.append(tr);
        }, 200)
    });
});
