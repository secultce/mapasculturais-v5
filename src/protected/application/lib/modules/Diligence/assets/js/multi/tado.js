$(document).ready(function () {
    //Plugin jquery Froala Editor
    var editor = new FroalaEditor('#conclusionTado');
   
    //Mascara de data
    $(".dateTado").mask('00/00/0000');
    $("#cpfManager").mask('000.000.000-00');
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
    // Obtém o valor do cookie ""erro-tado"
    var message = $.cookie("erro-tado");

    if (message) {
        // Se o cookie existir, exiba um alerta
        diligenceMessage.messageError("Ops!", message, 4000);
        //Removendo o cookie
        $.removeCookie("erro-tado", { path: '/' });
    }
});


function saveTado(editor, status)
{
    if ($("#nameManager").val() == '' || $("#cpfManager").val() == '') {
        Swal.fire({
            title: "Ops!",
            icon: "error",
            text: "Está faltando os dados do Coordenador"
        });
        return false;
    }
    // Dados para requisição
    const postDataTado = {
        numbertec: $("#numbertec").val(),
        dateDay: $("#dateTado").val(),
        datePeriodInitial: $("#periodInitial").val(),
        datePeriodEnd: $("#periodEnd").val(),
        object : $("#object").val(),
        conclusion: editor.html.get(),//Captura o html do textarea
        idTado: $("#idTado").val(),
        status : status,
        nameManager :  $("#nameManager").val(),
        cpfManager  : $("#cpfManager").val()
    };

    if($("#idTado").val() !== '') {
        postDataTado['idTado'] = $("#idTado").val();
    }
    
    if(status === 1){
        //VALIDANDO NO FRONTEND
        if($("#periodInitial").val() == '' || $("#periodEnd").val() == '') {
            Swal.fire({
                title: "Ops!",
                icon: "error",
                text: "O Período da vigência é obrigatório",
            });
            return false;
        }
        //CONFIRMAÇÃO
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
            if (result.isConfirmed) {            
                save(postDataTado);
            }
        });
    }else{
        save(postDataTado);
    }
   

}
//SALVANDO OS DADOS DEPENDENDO DO STATUS
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
                
                if(postDataTado.status == 1) {
                    setTimeout(() => {
                        const urlRedirect = MapasCulturais.createUrl('tado', 'gerar/'+MapasCulturais.idEntity);
                        window.location.href=urlRedirect;
                    }, 2000);
                }

                setTimeout(() => {
                    location.reload();
                }, 3000);
               
            }

            if(res.status == 403){
                const infoErro = [];
                res.data.forEach(element => {
                  infoErro.push('<li>'+element+'</li>')
                });
                const liInfoError = infoErro.join(' ');
                Swal.fire({
                    title: "Campo vazio!",
                    icon: "error",
                    html: `<ul class="ul-info">${liInfoError}</ul>`,
                });
            }

            if(res.status == 401){
                Swal.fire({
                    title: res.title,
                    text: res.message,
                    icon: "error"
                });
            }
        },
        error: function(err) {
            Swal.fire({
                title: res.title,
                text: res.message,
                icon: "error"
            });
        }
    });
}

function regenerateTado()
{
    location.href=MapasCulturais.createUrl('tado/gerar', MapasCulturais.idEntity);
}