$(document).ready(function () {
    // CKEDITOR.replace( 'conclusionTado' );
    var editor = CKEDITOR.replace('conclusionTado');

    $("#generateTado").click(function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: MapasCulturais.createUrl('tado', 'saveTado/' + MapasCulturais.idEntity),
            data: {
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
    });
});