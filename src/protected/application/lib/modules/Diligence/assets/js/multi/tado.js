$(document).ready(function () {
    // CKEDITOR.replace( 'conclusionTado' );
    var editor = CKEDITOR.replace('conclusionTado');
    //Mascara de data
    $("#dateTado").mask('00/00/0000');
    $( "#dateTado" ).datepicker({
        altFormat: "dd/mm/YYYY"
      });

    $("#generateTado").click(function (e) {
        e.preventDefault();
        Swal.fire({
            title: "Confirmar gerar o seu relatório?",
            text: "Essa ação não pode ser desfeita. Por isso, revise seu relatório com cuidado.",
            showConfirmButton: true,
            showCloseButton: false,
            showCancelButton: true,
            reverseButtons: true,
            cancelButtonText: `Não, enviar depois`,
            confirmButtonText: "Enviar agora",
            customClass: {
                confirmButton: "btn-success-rec",
                cancelButton: "btn-warning-rec"
            },
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: MapasCulturais.createUrl('tado', 'saveTado/' + MapasCulturais.idEntity),
                    data: {
                        dateDay: $("#dateTado").val(),
                        object : $("#object").val(),
                        conclusion: editor.getData()              
                    },
                    dataType: "json",
                    success: function (res) {
                        console.log('response');
                        if(res.status == 200){
                            Swal.fire({
                                title: "O seu documento foi gerado",
                                text: "Após baixar o documento, você pode editar e baixá-lo novamente enquanto estiver dentro do prazo",
                                icon: "success"
                            });
                        }
                    }
                });
            }
        });
        
    });
});