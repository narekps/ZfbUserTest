$(function() {
    var $contragentModal = $('#contragentModal'),
        $contragentEditModal = $('#contragentEditModal'),
        newForm = document.getElementById('contragentForm'),
        editForm = document.getElementById('contragentEditForm'),
        $addContragentBtn = $('#addContragentBtn'),
        $contragentSaveBtn = $('#contragentSaveBtn'),
        $contragentEditSaveBtn = $('#contragentEditSaveBtn'),
        $errorMsg = $('.error-msg', $contragentModal),
        $editErrorMsg = $('.error-msg', $contragentModal);

    $('input[name="etpContractDate"]').bootstrapMaterialDatePicker({
        time: false,
        switchOnClick: true,
        lang: 'ru'
    });
    $('input[name="phone"]').inputmask({"mask": "+7 (999) 999-99-99"}); //specifying options

    $('input').on('change', function () {
        var form = document.getElementById($(this).parents('form').eq(0).attr('id'));
        validateForm(form);
    });

    $addContragentBtn.on('click', function () {
        resetForm(newForm);
        $contragentModal.modal('show');
    });

    $contragentSaveBtn.on('click', function (e) {
        e.preventDefault();

        $errorMsg.hide();

        if (validateForm(newForm) !== true) {
            return;
        }

        var data = $(newForm).serialize(), url = $contragentSaveBtn.attr('data-save-url');

        var jqxhr = $.post(url, data, function (response) {
            successCallback(response, newForm, $contragentModal, $errorMsg);
        }).fail(function (response) {
            failCallback(response, newForm, $contragentModal, $errorMsg)
        }).always(function (response) {
        });
    });

    $contragentEditSaveBtn.on('click', function (e) {
        e.preventDefault();

        $errorMsg.hide();

        if (validateForm(editForm) !== true) {
            return;
        }

        var id = $('input[name="id"]', editForm).val(),
            data = $(editForm).serialize(),
            url = $contragentEditSaveBtn.attr('data-save-url');

        url = url.replace(':id', id);


        var jqxhr = $.post(url, data, function (response) {
            successCallback(response, editForm, $contragentEditModal, $editErrorMsg);
        }).fail(function (response) {
            failCallback(response, editForm, $contragentEditModal, $editErrorMsg)
        }).always(function (response) {
        });
    });

    $('.contragentEditBtn').on('click', function () {
        var $btn = $(this);

        $.get($btn.attr('data-get-url'), function (data) {
            if (data.success === true) {
                resetForm(editForm, data.contragent);
                $contragentEditModal.modal('show');
            }
        }).fail(function (response) {
            if (response.status === 403) {
                location.href = '/user/authentication';
            }
        });
    });

    function resetForm(form, values) {
        values = values || {};
        form.reset();
        $('.is-filled', form).removeClass('is-filled');

        for (var key in values) {
            if (!values.hasOwnProperty(key)) {
                continue;
            }

            $('[name="' + key + '"].' + key, form).val(values[key]);
            $('[name="' + key + '"].' + key, form).parents('.form-group').eq(0).addClass('is-filled');
        }

        validateForm(form)
    }

    function validateForm(form) {
        if (form.checkValidity() === true) {
            $('button[type="button"]', form).removeAttr('disabled');

            return true;
        }
        $('button[type="button"]', form).attr('disabled', 'disabled');
        return false;
    }

    function successCallback(response, form, $modal, $msg) {
        $('.invalid-feedback', form).html('');

        if (response.success) {
            resetForm(form);
            $modal.modal('hide');
            location.reload();
        } else if (response.formErrors) {
            showFormErrors(form, response.formErrors)
        } else if (response.message) {
            $msg.html(response.message);
            $msg.show();
        }
    }

    function failCallback(response, form, $modal, $msg) {
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
});
