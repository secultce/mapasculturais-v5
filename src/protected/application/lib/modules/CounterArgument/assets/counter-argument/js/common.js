const counterArgumentCommon = {
    view(text) {
        Swal.fire({
            title: 'Contrarrazão',
            html: text,
            width: 700,
        })
    },
    viewResponse(text) {
        Swal.fire({
            title: 'Resposta da Contrarrazão',
            html: text,
            width: 700,
        })
    }
}

$(() => {
    $('[btn-view-counter-argument]').on('click', function (event) {
        const text = event.currentTarget.dataset.text
        counterArgumentCommon.view(text)
    })

    $('[btn-view-counter-argument-response]').on('click', function (event) {
        const text = event.currentTarget.dataset.text
        counterArgumentCommon.viewResponse(text)
    })
})


$(() => {
    $('#counter-reason-admin-table').DataTable({
        dom: 'Bfrtip',
        paging: true,
        ordering: true,
        pageLength: 10,
        order: [[4, 'asc']],
        columnDefs: [
            { orderable: false, targets: [5, 6] },
            { visible: false, targets: 6 } 
        ],
        buttons: [
            {
                extend: 'csv',
                text: 'Exportar como CSV',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6], 
                    
                    format: {
                        body: function (data, row, column, node) {
                            let sourceText = data;
                            if (typeof data === 'string' && data.indexOf('data-text') > -1) {
                                let $tempContainer = $('<div>').html(data);
                                let $targetElement = $tempContainer.find('[data-text]');

                                if ($targetElement.length > 0) {
                                    let attrValue = $targetElement.attr('data-text');
                                    if (attrValue !== undefined && attrValue !== null) {
                                        sourceText = attrValue;
                                    }
                                }
                            }

                            if (typeof sourceText === 'string') {
                                let tempDiv = document.createElement("div");
                                tempDiv.innerHTML = sourceText;
                                let cleanText = tempDiv.textContent || tempDiv.innerText || "";
                                return cleanText.trim();
                            }

                            return sourceText;
                        }
                    }
                }
            },
        ],
        language: {
            "sEmptyTable": "Nenhum registro encontrado",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered": "(Filtrados de _MAX_ registros)",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "_MENU_ resultados por página",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sZeroRecords": "Nenhum registro encontrado",
            "sSearch": "Pesquisar",
            "oPaginate": {
                "sNext": "Próximo",
                "sPrevious": "Anterior",
                "sFirst": "Primeiro",
                "sLast": "Último"
            },
            "oAria": {
                "sSortAscending": ": Ordenar colunas de forma ascendente",
                "sSortDescending": ": Ordenar colunas de forma descendente"
            }
        }
    });
});
