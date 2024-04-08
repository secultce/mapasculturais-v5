$(document).ready(function () {
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
            
        console.log(res.message)
            $("#li-tab-diligence-diligence > a").remove();
            $("#li-tab-diligence-diligence").append('<label>Diligência</label>');
            $("#li-tab-diligence-diligence > label").addClass('cursor-disabled');
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

