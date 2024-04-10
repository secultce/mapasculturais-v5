$(document).ready(function () {
    EntityDiligence.hideCommon();
    let entityDiligence = EntityDiligence.showContentDiligence();
    entityDiligence
    .then((res) => {
        console.log({res})
        if (
            (res.message == 'sem_diligencia' || res.message == 'diligencia_aberta') &&
            MapasCulturais.userEvaluate == false) 
        {
            // if(res.data.length > 0){
            //     $("#descriptionDiligence").val(res.data[0].description);
            //     $("#btn-save-diligence").show();
            // }
            res.data.forEach((element, index) => {
                console.log({ element })
                console.log({ index })
                if (element.status == 3) {
                    console.log(element.description)
                    $("#paragraph_content_send_diligence").html(element.description);
                    $("#div-content-all-diligence-send").show();
                    $("#div-btn-actions-proponent").show();
                }else{
                    // $("#li-tab-diligence-diligence > a").remove();
                    // $("#li-tab-diligence-diligence").append('<label>Diligência</label>');
                    // $("#li-tab-diligence-diligence > label").addClass('cursor-disabled');
                }
            });
        console.log(res.message)
          
        }
    })
    .catch((error) => {
        console.log(error)
    })
});

//Sempre implementar esses metodos
function hideRegistration()
{
    EntityDiligence.hideRegistration();
}
function showRegistration()
{
    EntityDiligence.showRegistration();
}

