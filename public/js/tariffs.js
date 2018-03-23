$(function() {
    var $tariffModal = $('#tariffModal'),
        form = document.getElementById('tariffForm'),
        $addTariffBtn = $('#addTariffBtn'),
        $tariffSaveBtn = $('#tariffSaveBtn'),
        $errorMsg = $('.error-msg', $tariffModal);

    $('#saleEndDate').bootstrapMaterialDatePicker({
        time: false,
        switchOnClick: true,
        lang: 'ru',
        minDate: $('input[name="saleEndDate"]').attr('min')
    });

    $('input', form).on('change', function () {
        validateForm();
    });

    $addTariffBtn.on('click', function () {
        resetForm();
        $tariffModal.modal('show');
    });

    function resetForm(values) {
        values = values || {};
        form.reset();
        $('.is-filled', form).removeClass('is-filled');

        if (values.id) {
            $(form).attr('data-editing', 1);
            $('#newTariffModalLabel').text($tariffModal.attr('data-update-title'));
        } else {
            $(form).attr('data-editing', 0);
            $('#newTariffModalLabel').text($tariffModal.attr('data-create-title'));
        }

        for (var key in values) {
            if (!values.hasOwnProperty(key)) {
                continue;
            }

            if (key === 'published') {
                if (values[key]) {
                    $('[name="published"]').prop('checked', true);
                }
            } else {
                $('#' + key).val(values[key]);
                $('#' + key).parents('.form-group').eq(0).addClass('is-filled');
            }
        }

        $('#currency').parents('.form-group').eq(0).addClass('is-filled');
        $('#nds').parents('.form-group').eq(0).addClass('is-filled');
        validateForm()
    }

    function validateForm() {
        if (form.checkValidity() === true) {
            $tariffSaveBtn.removeAttr('disabled');

            return true;
        }
        $tariffSaveBtn.attr('disabled', 'disabled');
        return false;
    }

    $tariffSaveBtn.on('click', function (e) {
        e.preventDefault();

        $errorMsg.hide();

        if (validateForm() !== true) {
            return;
        }

        var data = $(form).serialize(), url;

        if ($(form).attr('data-editing') > 0) {
            var id = $('input[name="id"]', form).val();
            url = $tariffSaveBtn.attr('data-update-url');
            url = url.replace(':id', id);
        } else {
            url = $tariffSaveBtn.attr('data-create-url');
        }

        var jqxhr = $.post(url, data, successCallback).fail(failCallback).always(function (response) {
        });
    });

    function successCallback(response) {
        $('.invalid-feedback', form).html('');

        if (response.success) {
            resetForm();
            $tariffModal.modal('hide');
            location.reload();
        } else if (response.formErrors) {
            showFormErrors(form, response.formErrors)
        } else if (response.message) {
            $errorMsg.html(response.message);
            $errorMsg.show();
        }
    }

    function failCallback(response) {
        if (response.status === 403) {
            location.href = '/user/authentication';
        }
    }

    function showFormErrors(form, errors) {
        var fieldName, err;

        for (fieldName in errors) {
            if (!errors.hasOwnProperty(fieldName)) {
                continue;
            }

            var messages = [];
            for (err in errors[fieldName]) {
                if (!errors[fieldName].hasOwnProperty(err)) {
                    continue;
                }
                messages.push(errors[fieldName][err]);
            }

            var $input = $('[name="' + fieldName + '"]', form);
            var $invalidFeedback = $('.invalid-feedback', $input.parents('.form-group').eq(0));

            $invalidFeedback.html(messages.join("<br\>"));
            $input.addClass('is-invalid');
        }

        form.classList.add('was-validated');
    }

    $('.tariffEditBtn').on('click', function () {
        var $btn = $(this);

        $.get($btn.attr('data-get-url'), function (data) {
            if (data.success === true) {
                resetForm(data.tariff);
                $tariffModal.modal('show');
            }
        }).fail(function (response) {
            if (response.status === 403) {
                location.href = '/user/authentication';
            }
        });
    });

    $('.tariffArchiveBtn').on('click', function () {
        var $btn = $(this), url = $btn.attr('data-archive-url'),
            name = $btn.attr('data-name');

        swal({
            title: 'Вы действительно хотите архивировать тариф \"' + name + '\"?',
            text: "Действие нельзя будет отменить!",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0085FF',
            cancelButtonColor: '#FF2519',
            cancelButtonText: 'Нет',
            confirmButtonText: 'Да, архивировать!',
            showLoaderOnConfirm: true,
            allowOutsideClick: function () {
                return !swal.isLoading();
            },
            preConfirm: function () {
                return new Promise(function (resolve, reject) {
                    var jqxhr = $.post(url, {}, archiveCallback).fail(archiveCallback);

                    function archiveCallback(response) {
                        resolve(response);
                    }
                });
            }
        }).then(function (result) {
            if (result.dismiss) {
                return;
            }

            var response = result.value;

            if (response.success) {
                $btn.parents('.card').eq(0).addClass('archived');
                $btn.parents('.card-body').eq(0).html('<p>Тариф архивирован</p>');
                $btn.parents('.btn-group').eq(0).remove();
                swal({
                    title: 'Тариф архивирован!',
                    html: '<p>Тариф \"' + name + '\" успешно архивирован.<br/><br/><br/><small>Окно закроется через 5 секунд.</small></p>',
                    type: 'success',
                    timer: 5000,
                    onOpen: function () {
                        swal.showLoading();
                    }
                });
            } else if (response.status === 403) {
                location.href = '/user/authentication';
            } else {
                var msg = '<p>Не удалось архивировать тариф.</p>';
                if (response.message) {
                    msg += '<p>' + response.message + '</p>';
                }
                swal({
                    title: 'Ошибка',
                    html: msg,
                    type: 'error'
                });
            }
        });
    });
});
