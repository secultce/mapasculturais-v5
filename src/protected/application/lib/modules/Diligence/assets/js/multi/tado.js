$(document).ready(function () {
    // CKEDITOR.replace( 'conclusionTado' );
    var editor = CKEDITOR.replace('conclusionTado');

    $("#generateTado").click(function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: MapasCulturais.createUrl('tado', 'saveTado/' + MapasCulturais.idEntity),
            data: {
                conclusion: editor.getData()
            },
            dataType: "json",
            success: function (response) {
                console.log('response');
            }
        });
    });
});