jQuery('#account_deletion_button').confirm({
    title: 'Delete Account!',
    content: "<h4>Click button below to confirm Account Deletion.<h4>",
    boxWidth: '500px',
    useBootstrap: false,
    theme: 'custom',
    animation: 'zoom',
    closeAnimation: 'scale',
    typeAnimated: true,
    buttons: {
        CONFIRM: {
            text: 'CONFIRM',
            btnClass: 'btn btn-secondary btn-ds-secondary',
            action: function() {
                var action = $('#account_deletion_button').data('action');
                var nonce = $('#account_deletion_button').data('nonce');
                var account_deletion = $.post(
                    jsVariable.ajaxUrl, {
                        'action': action,
                        'nonce': nonce
                    }
                );

                $.alert({
                    title: 'Delete Account!',
                    boxWidth: '500px',
                    columnClass: 'loader',
                    useBootstrap: false,
                    theme: 'custom',
                    animation: 'zoom',
                    closeAnimation: 'scale',
                    typeAnimated: true,
                    content: '<img class="activation-img pt-3 pb-3" src="' + loader_gif + '"><p class="text-center"><b> Processing....</b></p>'
                });
                setTimeout(function() {
                    $('.jconfirm-buttons').hide();
                }, 10);

                account_deletion.done(function(response) {
                    $('.jconfirm-buttons button').trigger('click');
                    $.alert({
                        title: 'Delete Account!',
                        boxWidth: '500px',
                        columnClass: 'loader',
                        useBootstrap: false,
                        theme: 'custom',
                        animation: 'zoom',
                        closeAnimation: 'scale',
                        typeAnimated: true,
                        content: '<h4>Your request for account deletion proceeds successfully.</h4>'
                    });
                    setTimeout(function() {
                        $('.jconfirm-buttons button').on('click', function() {
                            window.location.reload();
                        });
                    }, 10);
                });

                account_deletion.fail(function(response) {
                    $('.jconfirm-buttons button').trigger('click');
                    $.alert({
                        title: 'Delete Account!',
                        boxWidth: '500px',
                        columnClass: 'loader',
                        useBootstrap: false,
                        theme: 'custom',
                        animation: 'zoom',
                        closeAnimation: 'scale',
                        typeAnimated: true,
                        content: '<h4> Error</h4><p class="mb-0">' + response.responseJSON.data.message + '</p>'
                    });
                    setTimeout(function() {
                        $('.jconfirm-buttons button').on('click', function() {
                            window.location.reload();
                        });
                    }, 10);

                })
            }
        },
        CANCLE: {
            text: 'CANCEL',
        }
    }
});