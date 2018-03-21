$(function() {
    var $invoiceModal = $('#invoiceModal'),
        form = document.getElementById('invoiceForm'),
        $addInvoiceBtn = $('#addInvoiceBtn'),
        $invoiceSaveBtn = $('#invoiceSaveBtn'),
        $errorMsg = $('.error-msg', $invoiceModal);

    $('#invoiceDate').bootstrapMaterialDatePicker({
        time: false,
        switchOnClick: true,
        lang: 'ru',
        minDate: $('input[name="invoiceDate"]').attr('min')
    });

    $('input', form).on('change', function () {
        validateForm();
    });

    $addInvoiceBtn.on('click', function () {
        resetForm();
        $invoiceModal.modal('show');
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
            $invoiceSaveBtn.removeAttr('disabled');

            return true;
        }
        $invoiceSaveBtn.attr('disabled', 'disabled');
        return false;
    }

    $invoiceSaveBtn.on('click', function (e) {
        e.preventDefault();

        $errorMsg.hide();

        if (validateForm() !== true) {
            return;
        }

        var data = $('#invoiceForm').serialize(),
            url = $invoiceSaveBtn.attr('data-save-url');

        var jqxhr = $.post(url, data, successCallback).fail(failCallback).always(function (response) {
        });
    });

    function successCallback(response) {
        $('.invalid-feedback', form).html('');

        if (response.success) {
            resetForm();
            $invoiceModal.modal('hide');
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
});
