
$(document).ready(function () {
    getContentDiligence()
    EntityDiligence.hideCommon();
    let entityDiligence = EntityDiligence.showContentDiligence();
    entityDiligence
    .then((res) => {
        console.log({res})

        if (
            (res.message == 'sem_diligencia' || res.message == 'diligencia_aberta') &&
            MapasCulturais.userEvaluate == true) 
        {
            if(res.data.length > 0 && res.data[0].status == 0){
                $("#descriptionDiligence").val(res.data[0].description);
                $("#btn-save-diligence").show();
            }
            res.data.forEach((element, index) => {
                console.log({element})
                console.log({index})
                if(element.status == 3){
                   EntityDiligence.formatDiligenceSendProponent(element);
                }
            });
            
            $("#answer_diligence").hide();
            $("#paragraph_info_status_diligence").html('A sua Diligência ainda não foi enviada');
        }
    })
    .catch((error) => {
        console.log(error)
    })
    
});

function getContentDiligence() {
    console.log('getContentDiligence')   
}

//Sempre implementar esses metodos
function hideRegistration()
{
    EntityDiligence.hideRegistration();
}
function showRegistration()
{
    EntityDiligence.showRegistration();
}

function showSaveContent(status)
{
    console.log({status})
    $("#label-save-content-diligence").show()
    setTimeout(() => {
        $("#label-save-content-diligence").hide()
        
    }, 2000);
    if(status == 3){
        window.location.href=MapasCulturais.createUrl('inscricao', MapasCulturais.entity.id);
    }
}

function saveDiligence(status) {
    console.log({status})
    if (status == 3) {
        Swal.fire({
            title: "Confirmar o envio da diligência?",
            text: "Essa ação não pode ser desfeita. Por isso, revise sua diligência com cuidado.",
            showDenyButton: true,
            showCancelButton: false,
            denyButtonText: `Não, enviar depois`,
            confirmButtonText: "Enviar agora",
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                sendAjaxDiligence(status)
            }
        });
    } else {
        sendAjaxDiligence(status)
    }

}
function sendAjaxDiligence(status) {
    $.ajax({
        type: "POST",
        url: MapasCulturais.createUrl('diligence', 'save'),
        data: {
            registration: MapasCulturais.entity.id,
            openAgent: MapasCulturais.userProfile.id,
            agent: MapasCulturais.entity.ownerId,
            createTimestamp: moment().format("YYYY-MM-DD"),
            description: $("#descriptionDiligence").val(),
            status: status,
        },
        dataType: "json",
        success: function(res) {
            console.log('sendAjax', res)
            if (res.status == 200) {
                console.log('status do envio', status)
                showSaveContent(status)
            }
        },
        error: function(err) {
            console.log({
                err
            })
        }
    });
}


