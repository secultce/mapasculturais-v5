var McMessages = (function () {

    /**
     * Exibe uma mensagem simples com título e texto
     * @param {string} title
     * @param {string} text
     * @param {number} [time] - Tempo em ms (opcional)
     */
    function basic(title) {
        Swal.fire(title);
    }

    function success(title, text, time = null) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'success',
            timer: time || 5000,
            showConfirmButton: true,
            allowOutsideClick: false,
            confirmButtonText: `<i class="fa fa-thumbs-up"></i> OK`,
        });
    }



    /**
     * Exibe um loading com spinner personalizado
     * @param {string} [title='Carregando...']
     * @param {string} [html] - HTML do spinner (opcional)
     */
    function loading(title = 'Aguarde...', html = null) {
        const spinnerHtml = html || '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>';

        Swal.fire({
            title: title,
            html: spinnerHtml,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            imageUrl: MapasCulturais.spinnerUrl,
            imageHeight: 30
        });
    }

    /**
     * Exibe mensagem de erro com ícone
     * @param {string} title
     * @param {string} text
     * @param {number} [time] - Tempo em ms (opcional)
     */
    function error(title, text, time = null) {
        Swal.fire({
            icon: 'error',
            title: title,
            text: text,
            timer: time || undefined,
            showConfirmButton: true,
            confirmButtonText: 'OK',
            allowOutsideClick: false
        });
    }

    /**
     * Exibe confirmação com botões personalizados
     * @param {string} titleQuestion
     * @param {string} textTrash
     * @param {string} [titleCancel='Cancelar']
     * @param {string} [titleConfirm='Confirmar']
     * @param {string} [classBtnConfirm='btn btn-success']
     * @param {string} [classBtnCancel='btn btn-secondary']
     * @returns {Promise} - Resolvida com isConfirmed: true/false
     */
    function messageConfirm(
        titleQuestion,
        textTrash,
        titleCancel = 'Cancelar',
        titleConfirm = 'Confirmar',
        classBtnConfirm = 'btn btn-success',
        classBtnCancel = 'btn btn-secondary',
        timerProgressBar = false,
        timer = null,
        allowOutsideClick = true
    ) {
        return Swal.fire({
            title: titleQuestion,
            text: textTrash,
            icon: 'warning',
            showCancelButton: true,
            showConfirmButton: true,
            reverseButtons: true,
            cancelButtonText: titleCancel,
            confirmButtonText: titleConfirm,
            customClass: {
                confirmButton: classBtnConfirm,
                cancelButton: classBtnCancel
            },
            buttonsStyling: false,
            focusConfirm: false,
            timerProgressBar: timerProgressBar,
            timer:timer,
            allowOutsideClick: allowOutsideClick
        });
    }

    function info(html = null, )
    {
        Swal.fire({
            title: 'Importante' ,
            icon: "info",
            html: html,
            showCloseButton: true,
            focusConfirm: true,
            confirmButtonText: `<i class="fa fa-thumbs-up"></i> OK, ciente`,
        });
    }

    return {
        basic,
        success,
        loading,
        error,
        messageConfirm: messageConfirm,
        info
    };

})();