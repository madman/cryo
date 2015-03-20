function PageManagement(actionManagementUrl, token, type) {
    var context;

    var reloadPage = function () {
        window.location.reload(true);
    }

    var toggleSuccessHandler = function () {
        if (null === context) {
            return reloadPage;
        }

        if (context.data('page-active')) {
            //become inactive
            context.removeClass('btn-success');
            context.data('page-active', '');
        } else {
            //become active
            context.addClass('btn-success');
            context.data('page-active', 1);
        }
        $('.toggle-icon', context).toggle();
        $('.page-slug', context.parent().parent()).toggle();
    }

    var successHandler = function (action) {
        if (action === 'delete') {
            return reloadPage;
        } else if (action === 'toggle') {
            return toggleSuccessHandler;
        }
    }

    var actionClickHandler = function (action, itemId) {
        $.ajax({
            type: 'post',
            url: actionManagementUrl,
            data: {
                token: token,
                id: itemId,
                action: action
            },
            success: successHandler(action)
        });
    }

    var removeActionClickHandler = function () {
        context = $(this);
        if (confirm(removeConfirmText+"'"+ context.data('page-name') + "'")) {
            actionClickHandler('delete', context.attr('data-page-id'));
        }

        return false;
    }

    var toggleActionClickHandler = function () {
        context = $(this);

        if (context.data('page-active')) {
            var confirmText = deactivateConfirmText;
        } else {
            var confirmText = activateConfirmText;

        }
        confirmText += " '" + context.data('page-name') + "'";

        if (confirm(confirmText)) {
            actionClickHandler('toggle', context.data('page-id'));
        }
        return false;
    }

    switch (type) {
        case 'actions':
            var removeConfirmText = 'Вы уверены что хотите удалить страницу акций';
            var activateConfirmText = 'Вы уверены что хотите активировать страницу акций';
            var deactivateConfirmText = 'Вы уверены что хотите деактивировать страницу акций';
            break;
        case 'news':
            var removeConfirmText = 'Вы уверены что хотите удалить страницу новостей';
            var activateConfirmText = 'Вы уверены что хотите активировать страницу новостей';
            var deactivateConfirmText = 'Вы уверены что хотите деактивировать страницу новостей';
            break;
    }

    $('.remove-page').click(removeActionClickHandler);
    $('.toggle-active-page').click(toggleActionClickHandler);
}

