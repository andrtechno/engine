window.yii.panixValidation = (function ($) {
    var pub = {
        isEmpty: function (value) {
            return value === null || value === undefined || ($.isArray(value) && value.length === 0) || value === '';
        },

        addMessage: function (messages, message, value) {
            messages.push(message.replace(/\{value\}/g, value));
        },
        ajaxSlug: function (value, messages, options) {
            var xhr;
            var alias = $('#seo_alias');
            alias.parent().append('<div id="alias_result"></div>');
            //if (translate_object_url == 0) {
            $('#title').keyup(function (event) {
                var title = $(this).val();
                if (options.usexhr) {
                    alias.val(ru2en.translit(title.toLowerCase())).addClass('loading');
                    // alias.parent().append('<div id="alias_result"></div>');
                } else {
                    alias.val(ru2en.translit(title.toLowerCase()));

                }

                if (alias.val().length > 2) {
                    $("#alias_result").hide();
                    if (options.usexhr) {
                        if (typeof xhr !== 'undefined')
                            xhr.abort();
                        xhr = $.ajax({
                            url: '/admin/app/ajax/checkalias',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                model: options.model,
                                alias: alias.val(),
                                isNew: options.pk
                            },
                            success: function (data) {
                                alias.removeClass("loading");
                                if (data.result) {
                                    alias.parent().parent().addClass('has-error').removeClass('has-success');
                                    $("#alias_result").html('<span class="label label-danger">' + data.message + '</span>').show();
                                } else {
                                    alias.parent().parent().addClass('has-success').removeClass('has-error');
                                    $("#alias_result").html('<span class="label label-success">' + data.message + '</span>').show();
                                }
                            }
                        });
                    }
                }
            });
        },
        slug: function (value, messages, options) {
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }

            if (options.defaultScheme && !/:\/\//.test(value)) {
                value = options.defaultScheme + '://' + value;
            }

            var valid = true;

            if (options.enableIDN) {
                var matches = /^([^:]+):\/\/([^\/]+)(.*)$/.exec(value);
                if (matches === null) {
                    valid = false;
                } else {
                    value = matches[1] + '://' + punycode.toASCII(matches[2]) + matches[3];
                }
            }

            if (!valid || !options.pattern.test(value)) {
                pub.addMessage(messages, options.message, value);
            }
        },

    };

    return pub;
})(jQuery);
