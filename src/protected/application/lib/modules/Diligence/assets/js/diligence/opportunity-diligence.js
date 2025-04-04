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
        if(e.target.value == 0) rowList.show()
        else rowList.each((_, row) =>
            row.getAttribute('data-status-filter') !== e.target.value
                ? row.style.display = 'none'
                : row.style.display = 'table-row')
    });


    $( "#subject-filter" ).on( "change", function(e) {
        console.log(e.target.value)
        const rowList = $('#diligences tbody tr');
        if(e.target.value == 'all') rowList.show()
        else rowList.each((_, row) =>
            row.getAttribute('data-subject-filter') !== e.target.value
                ? row.style.display = 'none'
                : row.style.display = 'table-row')
    } );    
});
