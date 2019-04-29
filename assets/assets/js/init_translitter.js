function init_translitter(options) {
    var xhr;
    var alias = $('#' + options.AttributeSlugId);
    alias.parent().append('<div id="alias_result"></div>');
    //if (translate_object_url == 0) {
    $('#' + options.attributeCompareId).keyup(function (event) {
        var title = $.trim($(this).val());
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
                        pk: options.pk,
                        attribute_slug: options.AttributeSlug,
                        successMessage: options.successMessage
                    },
                    success: function (data) {
                        alias.removeClass("loading");
                        if (data.result) {
                            alias.parent().parent().addClass('has-error').removeClass('has-success');
                            $("#alias_result").html('<span class="label label-danger">' + data.message + '</span>').show();
                        } else {
                            // alias.parent().parent().addClass('has-success').removeClass('has-error');
                            // $("#alias_result").html('<span class="label label-success">' + data.message + '</span>').show();
                        }
                    }
                });
            }
        }
    });
    // }
}


