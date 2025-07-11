const refo = {
    activeEventDeleteFinancialReport() {
        $('[delete-financial-report]').on('click', (event) => {
            Swal.fire({
                title: "Excluir Relatório Financeiro?",
                text: "Essa ação não poderá ser desfeita.",
                showCancelButton: true,
                cancelButtonText: "Cancelar",
                confirmButtonText: "Confirmar",
                reverseButtons: true
            }).then(res => {
                if (res.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: MapasCulturais.createUrl('refo', 'deleteFinancialReport'),
                        data: {
                            fileId: event.currentTarget.dataset.fileId
                        },
                        dataType: "json",
                        success() {
                            $(event.currentTarget).parents('#financial-report-wrapper').remove()
                            MapasCulturais.Messages.success('O relatório financeiro foi removido da prestação de contas')
                        },
                        error(err) {
                            if (err.status === 400) {
                                MapasCulturais.Messages.alert('O arquivo não pode ser removido, pois o TADO já foi gerado')
                                return
                            }
                            MapasCulturais.Messages.error('Ocorreu algum erro. Verifique e tente novamente.')
                        }
                    })
                }
            })
        })
    }
}

$(function () {
    // Retornando o valor da situação
    getSituacion()

    $("#situacion-refo-multi").on("change", function (event) {
        sendSituacion(event.target.value)

        $("#label-status-actual").html($(this).find("option:selected").text())
        $("#multi-div-btn-status").removeClass('d-none')

        if (event.target.value !== 'under_analysis') {
            $('.info-message-opinion').addClass('d-none')
            $('.opinion-form').removeClass('d-none')
        } else {
            $('.info-message-opinion').removeClass('d-none')
            $('.opinion-form').addClass('d-none')
        }
    })

    $('#import-financial-report .mc-submit').on('click', () => {
        setTimeout(() => {
            refo.activeEventDeleteFinancialReport()
        }, 1500)
    })

    refo.activeEventDeleteFinancialReport()
})

/**
 * Envia o situação para salvar ou alterar o valor
 * @param {string} valueSituacion 
 */
function sendSituacion(valueSituacion) {
    $.ajax({
        type: "POST",
        url: MapasCulturais.createUrl('refo', 'situacion'),
        data: { situacion: valueSituacion, entity: MapasCulturais.entity.id },
        dataType: "json",
        success: function (res) {
            if (res.status == 200) {
                MapasCulturais.Messages.success('Salvo');
            }
        },
        error: function (err) {
            MapasCulturais.Messages.error(err.responseJSON);
        }
    });
}

// Setando o valor cadastrado no banco
function getSituacion() {
    $.ajax({
        type: "GET",
        url: MapasCulturais.createUrl('refo', 'getSituacionPC/' + MapasCulturais.entity.id),
        dataType: "json",
        success: function (response) {
            if (response.situacion === 'all') {
                $("#p-btn-tado").hide();
            } else {
                $("#situacion-refo-multi").val(response.situacion).change();
            }
        }
    });
}
