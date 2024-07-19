var diligenceMessage = (function(){

    //mensagem simples
    function messageSimple(title, text, time)
    {
        Swal.fire({
            title: title,
            text: text,
            timer: time,
            showConfirmButton: true,
            reverseButtons: false,
        });
    }

    function loadSimple()
    {
        Swal.fire({
            title: "Abrindo a sua diligência",
            html: '<img src="' +  MapasCulturais.spinnerUrl + '" style="height: 24px" />',
            showConfirmButton: false,
        });
    }

    return {
        messageSimple: messageSimple,
        loadSimple: loadSimple
    };
}());
