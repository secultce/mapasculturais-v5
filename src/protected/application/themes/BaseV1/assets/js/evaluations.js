$(function(){
    var labels = MapasCulturais.gettext.evaluations;

    var $formContainer = $('#registration-evaluation-form');
    var $form = $formContainer.find('form');
    var $list = $('#registrations-list-container');
    var $header = $('#main-header');
    var __onChangeTimeout = null;
    var autoSaveRequest = null;
    var autoSaveQueued = false;
    var autoSaveCompletionCallbacks = [];
    var evaluationSubmitRequest = null;
    var evaluationSubmissionInProgress = false;
    var evaluationControlStates = null;

    function setEvaluationSubmitBusy($button, busy) {
        if (busy && !evaluationControlStates) {
            evaluationControlStates = [];
            $form.find(':input').each(function () {
                evaluationControlStates.push({
                    element: this,
                    disabled: this.disabled
                });
                this.disabled = true;
            });
        } else if (!busy && evaluationControlStates) {
            $.each(evaluationControlStates, function (index, state) {
                state.element.disabled = state.disabled;
            });
            evaluationControlStates = null;
        }

        $button.prop('disabled', busy)
            .toggleClass('is-disabled', busy)
            .attr('aria-busy', busy ? 'true' : 'false');
        $form.attr('aria-busy', busy ? 'true' : 'false');
    }

    function getEvaluationData() {
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

        return dataObject;
    }

    function autoSaveEvaluation() {
        if (evaluationSubmissionInProgress) {
            autoSaveQueued = false;
            return;
        }

        if (autoSaveRequest) {
            autoSaveQueued = true;
            return;
        }

        autoSaveQueued = false;

        var data = $form.serialize();
        var url = MapasCulturais.createUrl('registration', 'saveEvaluation', {'0': MapasCulturais.request.id, 'status': 'evaluated'});
        var request = $.post(url, data);

        autoSaveRequest = request;
        request.always(function () {
            var completionCallbacks = autoSaveCompletionCallbacks;
            autoSaveCompletionCallbacks = [];

            if (autoSaveRequest === request) {
                autoSaveRequest = null;
            }

            if (autoSaveQueued && !evaluationSubmissionInProgress) {
                autoSaveEvaluation();
            }

            $.each(completionCallbacks, function (index, callback) {
                callback();
            });
        });

        request.done(function () {
            MapasCulturais.Messages.success(labels.saveMessage);
        });
    }

    function submitEvaluation($button, url, dataObject) {
        if (evaluationSubmitRequest) {
            return;
        }

        var submitted = false;
        var request = $.post(url, { data: dataObject });
        evaluationSubmitRequest = request;

        request.done(function () {
            submitted = true;
        });

        // Register cleanup before UI callbacks so an unexpected response or
        // message error cannot leave a failed submission locked.
        request.always(function () {
            evaluationSubmitRequest = null;

            if (!submitted) {
                evaluationSubmissionInProgress = false;
                setEvaluationSubmitBusy($button, false);
            }
        });

        request.done(function () {
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

            MapasCulturais.Messages.success(labels.saveMessage);
        });

        request.fail(function (rs) {
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
    }

    $(window).scroll(function(){
        var top = parseInt($header.css('top'));
        /* $formContainer.css('margin-top', top);
        $list.css('margin-top', top); */
    });

    $formContainer.find('.js-evaluation-submit').on('click', function (e) {
        e.preventDefault();

        if (evaluationSubmissionInProgress) {
            return;
        }

        var $button = $(this);
        var url = MapasCulturais.createUrl('registration', 'saveEvaluation', {
            '0': MapasCulturais.request.id,
            'status': 'evaluated'
        });

        var isValid = true;
        $('.bonus-select').each(function () {
            var $select = $(this);
            var value = $select.val();

            if (value==="?") {
                isValid = false;
                MapasCulturais.Messages.error('Por favor, selecione uma opção válida em todos os campos de bônus.');
                $select.focus();
                return false;
            }

        });

        if (!isValid) {
            return;
        }

        // Freeze the exact data the evaluator confirmed before disabling the
        // controls and, if needed, waiting for an in-flight autosave.
        var dataObject = getEvaluationData();

        clearTimeout(__onChangeTimeout);
        __onChangeTimeout = null;
        autoSaveQueued = false;
        evaluationSubmissionInProgress = true;
        setEvaluationSubmitBusy($button, true);

        // An autosave already accepted by the server cannot be safely aborted.
        // Wait for it so the final write is always the last request.
        if (autoSaveRequest) {
            autoSaveCompletionCallbacks.push(function () {
                submitEvaluation($button, url, dataObject);
            });
        } else {
            submitEvaluation($button, url, dataObject);
        }
    });


    $(".autosave").on('keyup change', function() {
        clearTimeout(__onChangeTimeout);

        if (evaluationSubmissionInProgress) {
            __onChangeTimeout = null;
            return;
        }

        __onChangeTimeout = setTimeout(function(){
            __onChangeTimeout = null;
            autoSaveEvaluation();
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

                    $(assignBonusBtn).addClass('disabled');
                    MapasCulturais.Messages.alert('Aguarde! A bonificação está sendo atribuída.');

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

                            const removeBonusBtn = $(assignBonusBtn).siblings('.remove-bonus-btn');
                            $(removeBonusBtn).removeClass('disabled');
                        },
                        error() {
                            MapasCulturais.Messages.error('Erro ao atribuir bonificação. Verifique, e tente novamente.');
                        }
                    })
                }
            })
        }
    });

    $('body').on('click', '.remove-bonus-btn', function (event) {
        const removeBonusBtn = event.currentTarget;
        const disabledBtn = $(removeBonusBtn).hasClass('disabled');

        if (!disabledBtn) {
            const bonusAmount = MapasCulturais.evaluationConfiguration.bonusAmount;

            Swal.fire({
                title: "Você confirma a remoção?",
                text: `A bonificação será removida e ${bonusAmount} ponto(s) será(ão) subtraído(s) da nota do proponente.`,
                icon: "warning",
                showCancelButton: true,
                cancelButtonText: "Não, cancelar",
                confirmButtonText: "Confirmar remoção",
                confirmButtonColor: '#d33',
                reverseButtons: true
            }).then(res => {
                if (res.isConfirmed) {
                    const fieldId = removeBonusBtn.dataset.fieldId;

                    $(removeBonusBtn).addClass('disabled');
                    MapasCulturais.Messages.alert('Aguarde! A bonificação está sendo removida.');

                    $.ajax({
                        type: "PATCH",
                        url: MapasCulturais.createUrl('registration', 'removeBonus'),
                        data: {
                            registration_id: MapasCulturais.entity.id,
                            bonus_amount: bonusAmount,
                            field_id: fieldId
                        },
                        success() {
                            MapasCulturais.Messages.success('A bonificação foi removida do proponente');

                            const assignBtn = $(removeBonusBtn).siblings('.assign-bonus-btn');
                            $(assignBtn).removeClass('disabled');
                        },
                        error() {
                            MapasCulturais.Messages.error('Erro ao remover bonificação. Verifique e tente novamente.');
                        }
                    })
                }
            })
        }
    });

    $('input[type="number"]').on('wheel', function (e) {
        $(this).blur();
    });
});
