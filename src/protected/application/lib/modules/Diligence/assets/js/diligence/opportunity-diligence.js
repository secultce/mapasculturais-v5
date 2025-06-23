$(document).ready(function () {
    $('#evaluator-filter').on('change', e => {
        const rowList = $('#diligences tbody tr');
        if(e.target.value == 0) rowList.show()
        else rowList.each((_, row) =>
            row.getAttribute('data-evaluator-filter') !== e.target.value
                ? row.style.display = 'none'
                : row.style.display = 'table-row')
    });

    $('#status-filter').on('change', e => {
        const rowList = $('#diligences tbody tr');
        if (e.target.value === 'all') {
            e.target.children.item(0).selected = true;
            rowList.show()
        } else {
            rowList.each((_, row) =>
                row.getAttribute('data-status-filter') !== e.target.value
                    ? row.style.display = 'none'
                    : row.style.display = 'table-row');
        }
    });

    $("input[name=subject-filter]").on( "change", function(e) {
        const rowList = $('#diligences tbody tr');

        const checkboxes = document.querySelectorAll('input[name=subject-filter]:checked');
        const filters = Array.from(checkboxes).map(checkbox => checkbox.value);

        if(filters.length === 0) {
            rowList.show()
        } else {
            rowList.each((_, row) => {
                const subjectFilter = row.getAttribute('data-subject-filter');
                let show = true;

                for (const filter of filters) {
                    if (!subjectFilter.includes(filter)) {
                        show = false;
                        break;
                    }
                }

                if (show) {
                    row.style.display = 'table-row'
                } else {
                    row.style.display = 'none';
                }
            })
        }
    } );    
});
