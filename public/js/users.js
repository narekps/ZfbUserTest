$(function() {
    var $userModal = $('#userModal'),
        form = document.getElementById('addUserForm'),
        $addUserBtn = $('#addUserBtn'),
        $userSaveBtn = $('#userSaveBtn'),
        $errorMsg = $('.error-msg', $userModal);

    $('input', form).on('change', function () {
        validateForm();
    });

    $addUserBtn.on('click', function () {
        resetForm();
        $userModal.modal('show');
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

        validateForm()
    }

    function validateForm() {
        if (form.checkValidity() === true) {
            $userSaveBtn.removeAttr('disabled');

            return true;
        }
        $userSaveBtn.attr('disabled', 'disabled');
        return false;
    }

    $userSaveBtn.on('click', function (e) {
        e.preventDefault();

        $errorMsg.hide();

        if (validateForm() !== true) {
            return;
        }

        var data = $('#addUserForm').serialize(),
            url = $userSaveBtn.attr('data-save-url');

        var jqxhr = $.post(url, data, successCallback).fail(failCallback).always(function (response) {
        });
    });

    function successCallback(response) {
        $('.invalid-feedback', form).html('');

        if (response.success) {
            resetForm();
            $userModal.modal('hide');
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

    $('.userEditBtn').on('click', function () {
        var $btn = $(this);

        $.get($btn.attr('data-get-url'), function (data) {
            if (data.success === true) {
                resetForm(data.user);
                $userModal.modal('show');
            }
        }).fail(function (response) {
            if (response.status === 403) {
                location.href = '/user/authentication';
            }
        });
    });
});