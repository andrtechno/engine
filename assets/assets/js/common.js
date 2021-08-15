var common = window.common || {};
common.notify_list = Array;
common.getMsg = function (code) {
    return this.lang[this.language][code];
};
common.clipboard = function (selector) {
    var clipboard = new Clipboard(selector);
    clipboard.on('success', function () {
        //common.notify('Скопировано', 'info');
    });
};
common.switchInputPass = function (that, inputId) {
    var s = $('#' + inputId);
    var inp = (s.attr('type') === 'input');
    if (inp) {
        $(that).find('i').removeClass('icon-eye-close').addClass('icon-eye');
    } else {
        $(that).find('i').removeClass('icon-eye').addClass('icon-eye-close');
    }
    s.attr('type', inp ? 'password' : 'input');
};
common.notify = function (text, type) {
    var t = (type === 'error') ? 'danger' : type;
    if (common.isDashboard) {

        this.notify_list[0] = $.notify({message: text}, {
            type: t,
            allow_dismiss: false,
            placement: {
                from: "bottom",
                align: "left"
            },
            template: '<div data-notify="container" class="alert alert-{0}" role="alert"><button type="button" aria-hidden="true" class="close" data-notify="dismiss">&times;</button><span data-notify="icon"></span> <span data-notify="title">{1}</span> <span data-notify="message">{2}</span><div class="progress" data-notify="progressbar"><div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div><a href="{3}" target="{4}" data-notify="url"></a></div>'
        });
    } else {
        this.notify_list[0] = $.notify({message: text}, {
            type: t,
            delay:1,
            timer:($(document).width() > 768)?700:500,
            allow_dismiss: false,
            template: '<div data-notify="container" class="alert alert-{0}" role="alert"><button type="button" aria-hidden="true" class="close" data-notify="dismiss">&times;</button><span data-notify="icon"></span> <span data-notify="title">{1}</span> <span data-notify="message">{2}</span><div class="progress" data-notify="progressbar"><div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div><a href="{3}" target="{4}" data-notify="url"></a></div>'
        });
    }

};
common.url = function (url) {
    if (common.language_default !== common.language) {
        return common.language + '/' + url;
    }
    return url;
};
common.geoip = function (ip) {
    // common.flashMessage = true;

    var geoSelector = $('#geo-dialog');
    $('body').append('<div id=\"geo-dialog\"></div>');
    $.ajax({
        url: common.url('/admin/app/ajax/geo?ip=' + ip),
        type: 'GET',
        dataType: 'html',
        beforeSend: function () {

        },
        success: function (result) {
            console.log(geoSelector);
            geoSelector.dialog({
                model: true,
                responsive: true,
                resizable: false,
                height: 'auto',
                minHeight: 95,
                title: 'Информация о ' + ip,
                width: 700,
                draggable: false,
                modal: true,
                open: function () {
                    $('.ui-widget-overlay').bind('click', function () {
                        geoSelector.dialog('close');
                    });
                },
                close: function () {
                    geoSelector.remove();
                }
            });
            geoSelector.html(result);
            $('.ui-dialog').position({
                my: 'center',
                at: 'center',
                of: window,
                collision: 'fit'
            });
        }
    });
};
common.close_alert = function (aid) {
    $('#alert' + aid).fadeOut(1000);
    $.cookie('alert' + aid, true, {
        expires: 1, // one day
        path: '/'
    });
};
common.hasChecked = function (has, classes) {
    if ($(has).is(':checked')) {
        $(classes).removeClass('hidden');
    } else {
        $(classes).addClass('hidden');
    }
};
common.addLoader = function (text) {
    var t;
    if (text !== undefined) {
        t = text;
    } else {
        t = common.message.loading;
    }
    $('body').append('<div class="common-ajax-loading">' + t + '</div>');
};
common.removeLoader = function () {
    $('.common-ajax-loading').remove();
};
common.init = function () {
    console.log('common.init');
};
common.ajax = function (url, data, success, dataType, type) {
    var t = this;
    $.ajax({
        url: url,
        type: (type === undefined) ? 'POST' : type,
        data: data,
        dataType: (dataType === undefined) ? 'html' : dataType,
        beforeSend: function (xhr) {
            // if(t.ajax.beforeSend.message){
            //t.report(t.ajax.beforeSend.message);
            //}else{
            // t.report(t.getText('loadingText'));
            //}

        },
        error: function (xhr, textStatus, errorThrown) {
            t.notify(textStatus + ' ajax() ' + xhr.status + ' ' + xhr.statusText, 'error' + errorThrown);
            //t.report(textStatus+' ajax() '+xhr.responseText);
        },
        success: success

    });
};
common.setText = function (param, text) {
    this.lang[this.language][param] = text;
};
common.getText = function (param) {
    return common.lang[this.language][param];
};

common.enterSubmit = function (formid) {
    $(formid).keydown(function (event) {
        if (event.which === 13) {
            // event.preventDefault();
            $(formid).submit();
        }
    });
};

common.init();

$(document).ready(function () {
    $(document).on('pjax:send', function () {
        $('.grid-loading').show();
    });
    $(document).on('pjax:complete', function () {
        $('.grid-loading').hide();
    });

    $(document).on('click', '.editgrid', function () {
        var gridid = $(this).attr('data-grid-id');
        var modelClass = $(this).attr('data-model');
        var pjaxId = $(this).attr('data-pjax-id');
        console.log(gridid);
        $('body').append($('<div/>', {
            'id': gridid + '-dialog'
        }));
        $('#' + gridid + '-dialog').dialog({
            modal: true,
            autoOpen: true,
            width: 500,
            height: 'auto',
            title: 'Доспутные ячейки',
            responsive: true,
            resizable: false,
            draggable: false,
            create: function (event, ui) {

            },
            open: function () {
                var that = this;
                $.ajax({
                    url: '/admin/default/get-grid',
                    type: 'POST',
                    data: {
                        modelClass: modelClass,
                        grid: gridid
                    },
                    success: function (data) {
                        $(that).html(data);
                        $('.ui-dialog').position({
                            my: 'center',
                            at: 'center',
                            of: window,
                            collision: 'fit'
                        });
                    }
                });
            },
            close: function () {
                $(this).remove();
            },
            buttons: [{
                'text': common.message.save,
                "class": 'btn btn-success',
                'click': function (e) {

                    var form = $('#edit_grid_columns_form').serialize();
                    $.ajax({
                        url: '/admin/default/get-grid',
                        type: 'POST',
                        data: form,
                        beforeSend: function () {
                            $(e.currentTarget).attr('disabled', true).addClass('load');
                        },
                        success: function () {
                            $('#' + gridid + '-dialog').remove();
                            //$.pjax.reload({container: '#pjax-test'});
                            $.pjax.reload('#' + pjaxId, {timeout: false});
                            //$('#w0').yiiGridView('applyFilter');
                            $('#dialog-overlay').remove();
                        },
                        complete: function () {
                            $(e.currentTarget).attr('disabled', true).removeClass('load');
                        }
                    });
                }
            },
                {
                    'text': common.message.cancel,
                    "class": 'btn btn-secondary',
                    'click': function () {
                        $('#' + gridid + '-dialog').remove();
                    }
                }]
        });
    });

    $(document).on('click', '.switch', function (e) {
        e.preventDefault();

        var that = $(this);
        var url = that.attr('href');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    that.attr('href', data.url);
                    if (data.value) {
                        that.removeClass('btn-outline-success').addClass('btn-outline-secondary');
                        that.find('i').removeClass('icon-eye').addClass('icon-eye-close');
                    } else {
                        that.removeClass('btn-outline-secondary').addClass('btn-outline-success');
                        that.find('i').removeClass('icon-eye-close').addClass('icon-eye');
                    }
                    common.notify(data.message, 'success');
                } else {
                    common.notify(data.message, 'error');
                }
            }
        });
    });
});


yii.confirm = function (message, ok, cancel) {
    bootbox.confirm(
        {
            //title:'test',
            message: message,
            buttons: {
                confirm: {
                    label: common.message.ok,
                    className: 'btn-success'
                },
                cancel: {
                    label: common.message.cancel,
                    className: 'btn-outline-secondary'
                }
            },
            callback: function (confirmed) {
                if (confirmed) {
                    !ok || ok();
                } else {
                    !cancel || cancel();
                }
            }
        }
    );
    // confirm will always return false on the first call
    // to cancel click handler
    return false;
};
common.logger = function (name) {
    return {
        debug: function () {
            if (!console.debugging) return;
            var mainArguments = Array.prototype.slice.call(arguments);
            mainArguments.unshift("["+name+"]");
            console.log.apply(this, mainArguments);
        },
        warn: function () {
            if (!console.debugging) return;
            var mainArguments = Array.prototype.slice.call(arguments);
            mainArguments.unshift("["+name+"]");
            console.warn.apply(this, mainArguments);
        },
        error: function () {
            if (!console.debugging) return;
            var mainArguments = Array.prototype.slice.call(arguments);
            mainArguments.unshift("["+name+"]");
            console.error.apply(this, mainArguments);
        },
    };
}
