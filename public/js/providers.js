$(function() {
    var $newProviderModal = $('#newProviderModal'),
        form = document.getElementById('addUserForm'),
        $newProviderSaveBtn = $('#newProviderSaveBtn'),
        $errorMsg = $('.error-msg', $newProviderModal);

    $('#etpContractDate').bootstrapMaterialDatePicker({
        time: false,
        switchOnClick: true,
        lang: 'ru'
    });

    $('#phone').inputmask({"mask": "+7 (999) 999-99-99"}); //specifying options

    $('input', form).on('change', function () {
        validateForm();
    });

    $newProviderModal.on('show.bs.modal', function (e) {
        resetForm();
    });

    function resetForm() {
        form.reset();
        $('.is-filled', form).removeClass('is-filled');
    }

    function validateForm() {
        if (form.checkValidity() === true) {
            $newProviderSaveBtn.removeAttr('disabled');

            return true;
        }
        $newProviderSaveBtn.attr('disabled', 'disabled');
        return false;
    }

    $newProviderSaveBtn.on('click', function (e) {
        e.preventDefault();

        $errorMsg.hide();

        if (validateForm() !== true) {
            return;
        }

        var data = $('#addUserForm').serialize(),
            url = $newProviderSaveBtn.attr('data-save-url');

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
            $newProviderModal.modal('hide');
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
});