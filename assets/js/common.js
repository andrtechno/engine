
var common = window.CMS_common || {};
common = {
    debug: true,
    language: 'en',
    flashMessage: true,
    token: null,
    isDashboard: false,
    notify_list:[],
    getMsg: function (code) {
        return this.lang[this.language][code];
    },
    notify: function (text, type) {
        var t = (type == 'error') ? 'danger' : type;
        if (common.isDashboard) {

            this.notify_list[0] = $.notify({message: text}, {
                type: t,
                allow_dismiss: false,
                placement: {
                    from: "bottom",
                    align: "left"
                }
            });
        } else {
            this.notify_list[0] = $.notify({message: text}, {
                type: t,
                allow_dismiss: false
            });
        }

    },
    geoip: function (ip) {
        // common.flashMessage = true;


        $.ajax({
            url: '/admin/app/ajax/geo/ip/' + ip,
            type: 'GET',
            dataType: 'html',
            beforeSend: function () {
                $('body').append('<div id=\"geo-dialog\"></div>');
            },
            success: function (result) {
                $('#geo-dialog').dialog({
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
                            $('#geo-dialog').dialog('close');
                        });
                    },
                    close: function () {
                        $('#geo-dialog').remove();
                    }
                });

                $('#geo-dialog').html(result);

                $('.ui-dialog').position({
                    my: 'center',
                    at: 'center',
                    of: window,
                    collision: 'fit'
                });
            }
        });
    },
    close_alert: function (aid) {
        $('#alert' + aid).fadeOut(1000);
        $.cookie('alert' + aid, true, {
            expires: 1, // one day
            path: '/'
        });
    },
    hasChecked: function (has, classes) {
        if ($(has).is(':checked')) {
            $(classes).removeClass('hidden');
        } else {
            $(classes).addClass('hidden');
        }
    },
    addLoader: function (text) {
        if (text !== undefined) {
            var t = text;
        } else {
            var t = common.message.loading;
        }
        $('body').append('<div class="common-ajax-loading">' + t + '</div>');

    },
    removeLoader: function () {
        $('.common-ajax-loading').remove();
    },
    init: function () {
        console.log('common.init');
    },
    ajax: function (url, data, success, dataType, type) {
        var t = this;
        $.ajax({
            url: url,
            type: (type == undefined) ? 'POST' : type,
            data: data,
            dataType: (dataType == undefined) ? 'html' : dataType,
            beforeSend: function (xhr) {
                // if(t.ajax.beforeSend.message){
                //t.report(t.ajax.beforeSend.message);
                //}else{
                // t.report(t.getText('loadingText'));
                //}

            },
            error: function (xhr, textStatus, errorThrown) {
                t.notify(textStatus + ' ajax() ' + xhr.status + ' ' + xhr.statusText,'error');
                //t.report(textStatus+' ajax() '+xhr.responseText);
            },
            success: success

        });
    },
    setText: function (param, text) {
        this.lang[this.language][param] = text;
    },
    getText: function (param) {
        return common.lang[this.language][param];
    },
    enterSubmit: function (formid) {
        $(formid).keydown(function (event) {
            if (event.which === 13) {
                // event.preventDefault();
                $(formid).submit();
            }
        });
    }
};
common.init();
