$(document).ready(function () {
    // CKEDITOR.replace( 'conclusionTado' );
    var editor = CKEDITOR.replace('conclusionTado');
    //Mascara de data
    $("#dateTado").mask('00/00/0000');
    $( ".dateTado" ).datepicker({
        altFormat: "dd/mm/YYYY"
    });
    //Carregando junto com o DOM por conta do CKEDITOR
    $("#generateTado").click(function (e) {
        e.preventDefault();
        saveTado(editor, 1);
    });
    $("#draftTado").click(function (e) {
        e.preventDefault();
        saveTado(editor, 0);
    });
});

function saveTado(editor, status)
{
    //Dados para requisição
    const postDataTado = {
        dateDay: $("#dateTado").val(),
        datePeriodInitial: $("#periodInitial").val(),
        datePeriodEnd: $("#periodEnd").val(),
        object : $("#object").val(),
        conclusion: editor.getData(),
        idTado: $("#idTado").val(),
        status : status
    };
    if($("#idTado").val() !== '') {
        postDataTado['idTado'] = $("#idTado").val();
    }
    
    if(status === 1){
        if($("#periodInitial").val() == '' || $("#periodEnd").val() == '') {
            Swal.fire({
                title: "Ops!",
                icon: "error",
                text: "O Período da de vigência é obrigatório",
            });
            return false;
        }

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
                save(postDataTado);
            }
        });
    }else{
        console.log({postDataTado});
        save(postDataTado);
    }
   

}

function save(postDataTado)
{
     $.ajax({
        type: "POST",
        url: MapasCulturais.createUrl('tado', 'saveTado/' + MapasCulturais.idEntity),
        data: postDataTado,
        dataType: "json",
        success: function (res) {
          
            if(res.status == 200){
                //"O seu documento foi gerado"
                //"Após baixar o documento, você pode editar e baixá-lo novamente enquanto estiver dentro do prazo"
                Swal.fire({
                    title: res.title,
                    text: res.message,
                    icon: "success"
                });
            }

            if(res.status == 403){
                const infoErro = [];
                res.data.forEach(element => {
                  infoErro.push('<li>'+element+'</li>')
                });
                const liInfoError = infoErro.join(' ');
                Swal.fire({
                    title: "Ops! Observer",
                    icon: "error",
                    html: `<ul class="ul-info">${liInfoError}</ul>`,
                });
            }
        },
        error: function(err) {
            
        }
    });
}