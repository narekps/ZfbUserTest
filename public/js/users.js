$(function() {
    var $userModal = $('#userModal'),
        $updateUserModal = $('#updateUserModal'),
        newForm = document.getElementById('contragentForm'),
        updateUserForm = document.getElementById('updateUserForm'),
        $addUserBtn = $('#addUserBtn'),
        $userSaveBtn = $('#userSaveBtn'),
        $updateUserSaveBtn = $('#updateUserSaveBtn'),
        $errorMsg = $('.error-msg', $userModal),
        $updateErrorMsg = $('.error-msg', $updateUserModal);

    $('input').on('change', function () {
        var form = document.getElementById($(this).parents('form').eq(0).attr('id'));
        if (form) {
            validateForm(form);
        }
    });

    $addUserBtn.on('click', function () {
        resetForm(newForm);
        $userModal.modal('show');
    });

    $userSaveBtn.on('click', function (e) {
        e.preventDefault();

        $errorMsg.hide();

        if (validateForm(newForm) !== true) {
            return;
        }

        var data = $(newForm).serialize(),
            url = $userSaveBtn.attr('data-save-url');

        var jqxhr = $.post(url, data, function (response) {
            successCallback(response, newForm, $userModal, $errorMsg);
        }).fail(function (response) {
            failCallback(response, newForm, $userModal, $errorMsg)
        }).always(function (response) {
        });
    });

    $updateUserSaveBtn.on('click', function (e) {
        e.preventDefault();

        $updateErrorMsg.hide();

        if (validateForm(updateUserForm) !== true) {
            return;
        }

        var id = $('input[name="id"]', updateUserForm).val(),
            data = $(updateUserForm).serialize(),
            url = $updateUserSaveBtn.attr('data-save-url');

        url = url.replace(':id', id);


        var jqxhr = $.post(url, data, function (response) {
            successCallback(response, updateUserForm, $updateUserModal, $updateErrorMsg);
        }).fail(function (response) {
            failCallback(response, updateUserForm, $updateUserModal, $updateErrorMsg)
        }).always(function (response) {
        });
    });

    $('.userEditBtn').on('click', function () {
        var $btn = $(this);

        $.get($btn.attr('data-get-url'), function (data) {
            if (data.success === true) {
                resetForm(updateUserForm, data.user);
                $updateUserModal.modal('show');
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

    $('.userDeleteBtn').on('click', function () {
        var $btn = $(this), url = $btn.attr('data-delete-url'),
            username = $btn.attr('data-name');

        swal({
            title: 'Вы действительно хотите удалить пользователя ' + username + ' ?',
            text: "Действие нельзя будет отменить!",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0085FF',
            cancelButtonColor: '#FF2519',
            cancelButtonText: 'Нет',
            confirmButtonText: 'Да, удалить!'
        }).then(function (result) {
            if (result.value) {
                var jqxhr = $.post(url, {}, deleteCallback).fail(deleteCallback);
            }
        });

        function deleteCallback(response) {
            if (response.success) {
                $btn.parents('.user_block').eq(0).remove();
                swal(
                    'Удален!',
                    'Пользователь ' + username + ' успешно удален.',
                    'success'
                );
            } else if (response.status === 403) {
                location.href = '/user/authentication';
            } else {
                swal(
                    'Ошибка',
                    'Не удалось удалить пользователя.',
                    'error'
                );
            }
        }
    });
});
