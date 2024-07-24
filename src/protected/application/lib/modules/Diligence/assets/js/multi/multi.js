$(document).ready(function () {
    console.log('multi,js')
    $( "#situacion-refo-multi" ).on( "change", function(e) {
        console.log(e.target.value)

        if(e.target.value == 'disapproved')
        {
            Swal.fire({
                title: "Confirmar gerar o relatório?",
                text: "Essa ação não pode ser desfeita. Irá gerar um pdf para ser enviado ao financeiro.",
                showConfirmButton: true,
                showCloseButton: false,
                showCancelButton: true,
                reverseButtons: true,
                cancelButtonText: `Não, desistir`,
                confirmButtonText: "Gerar Relatório",
                customClass: {
                    confirmButton: "btn-success-rec",
                    cancelButton: "btn-warning-rec"
                },
            }).then((result) => {
                if (result.isConfirmed) {            
                    console.log(result)
                    $( "#situacion-refo-multi" ).prop('disabled', true);
                }
            });
        }
    });
});