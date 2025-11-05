$(function(){
    var labels = MapasCulturais.gettext.evaluations;

    var $formContainer = $('#registration-evaluation-form');
    var $form = $formContainer.find('form');
    var $list = $('#registrations-list-container');
    var $header = $('#main-header');
    $(window).scroll(function(){
        var top = parseInt($header.css('top'));
        /* $formContainer.css('margin-top', top);
        $list.css('margin-top', top); */
    });

    $formContainer.find('.js-evaluation-submit').on('click', function (e) {
        e.preventDefault();
        
        var $button = $(this);
        var url = MapasCulturais.createUrl('registration', 'saveEvaluation', {
            '0': MapasCulturais.request.id,
            'status': 'evaluated'
        });

        var data = $form.serializeArray();

        $('.bonus-select').each(function () {
            var fieldId = $(this).data('field-id');
            var value = angular.element(this).scope().field.bonused;
            var boolValue = (value == "true" || value === true) ? "true" : "false";
            data.push({ name: `b2_${fieldId}`, value: boolValue });
        });

        var dataObject = {};
        $.each(data, function (i, field) {
            var match = field.name.match(/^data\[(.+?)\]$/);
            if (match) {
                dataObject[match[1]] = field.value;
            } else {
                dataObject[field.name] = field.value;
            }
        });
        
        $.post(url, { data: dataObject }, function (r) {
            MapasCulturais.Messages.success(labels.saveMessage);

            if ($button.hasClass('js-next')) {
                var $current = $(".current");
                var $next = $current.nextAll('.visible:first');
                var $link = $next.find('a');

                if ($current.find('a').attr('href') == $next.find('a').attr('href')) {
                    $link = $(".registration-item:eq(2)").find('a');
                }

                if ($link.attr('href')) {
                    document.location = $link.attr('href');
                }
            }
        }).fail(function (rs) {
            if (rs.responseJSON && rs.responseJSON.error) {
                if (rs.responseJSON.data instanceof Array) {
                    rs.responseJSON.data.forEach(function (msg) {
                        MapasCulturais.Messages.error(msg);
                    });
                } else {
                    MapasCulturais.Messages.error(rs.responseJSON.data);
                }
            }
        });
    });


    var __onChangeTimeout;
    $(".autosave").on('keyup change', function() {
        clearTimeout(__onChangeTimeout);
        __onChangeTimeout = setTimeout(function(){
            var data = $form.serialize();
            var url = MapasCulturais.createUrl('registration', 'saveEvaluation', {'0': MapasCulturais.request.id, 'status': 'evaluated'});
            $.post(url, data, function(r){
                MapasCulturais.Messages.success(labels.saveMessage);
            });
        },15000);

    });

    $('body').on('click', '.assign-bonus-btn', function (event) {
        const assignBonusBtn = event.currentTarget;
        const disabledBtn = $(assignBonusBtn).hasClass('disabled');

        if (!disabledBtn) {
            const bonusAmount = MapasCulturais.evaluationConfiguration.bonusAmount;

            Swal.fire({
                title: "Você confirma a atribuição?",
                text: `A bonificação aumentará em ${bonusAmount} ponto(s) a nota do proponente.`,
                showCancelButton: true,
                cancelButtonText: "Não, fazer depois",
                confirmButtonText: "Confirmar",
                reverseButtons: true
            }).then(res => {
                if (res.isConfirmed) {
                    const fieldId = assignBonusBtn.dataset.fieldId;

                    $.ajax({
                        type: "PATCH",
                        url: MapasCulturais.createUrl('registration', 'assignBonus'),
                        data: {
                            registration_id: MapasCulturais.entity.id,
                            bonus_amount: bonusAmount,
                            field_id: fieldId
                        },
                        success() {
                            MapasCulturais.Messages.success('A bonificação foi atribuída ao proponente');
                            $(assignBonusBtn).addClass('disabled')
                        },
                        error() {
                            MapasCulturais.Messages.error('Erro ao atribuir bonificação. Verifique, e tente novamente.');
                        }
                    })
                }
            })
        }
    });
});
