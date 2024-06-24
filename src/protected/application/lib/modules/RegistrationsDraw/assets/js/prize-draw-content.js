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
        .then(response => response.json())
        .then(data => {
            renderRanking(data);
        })
        .catch(error => {
            console.error(error);
        });

    const renderRanking = arrayRanking => {
        const tableBodyElement = $('table#ranking-table tbody');
        tableBodyElement.children().each((key, elem) => elem.style.display = 'none');

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
    }
});
