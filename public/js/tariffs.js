$(function() {
    var $tariffModal = $('#tariffModal'),
        form = document.getElementById('tariffForm'),
        $addTariffBtn = $('#addTariffBtn'),
        $tariffSaveBtn = $('#tariffSaveBtn'),
        $errorMsg = $('.error-msg', $tariffModal);

    $('#saleEndDate').bootstrapMaterialDatePicker({
        time: false,
        switchOnClick: true,
        lang: 'ru'
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

        for (var key in values) {
            if (!values.hasOwnProperty(key)) {
                continue;
            }

            $('#' + key).val(values[key]);
            $('#' + key).parents('.form-group').eq(0).addClass('is-filled');
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

        var data = $('#tariffForm').serialize(),
            url = $tariffSaveBtn.attr('data-save-url');

        var jqxhr = $.post(url, data, successCallback).fail(failCallback).always(function (response) {
        });
    });

    function successCallback(response) {
        console.log("success");
        console.log(response);
        console.log(response.success);

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
        console.log("error");
        console.log(response);

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
        });
    });
});
