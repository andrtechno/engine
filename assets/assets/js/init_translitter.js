function init_translitter(options) {
    var xhr;
    var alias = $('#' + options.AttributeSlugId);
    alias.parent().append('<div id="alias_result"></div>');
    //if (translate_object_url == 0) {
    $('#' + options.attributeCompareId).keyup(function (event) {
        var title = $.trim($(this).val());
        var that = $(this);
      //  .replace(reg, ru2en.ru2en[a])
        var value = ru2en.translit(title.toLowerCase());
        if(options.replacement !== '-'){
            console.log(options.replacement);
            value.replace('/-/g', options.replacement);
        }
       // console.log(value);
        if (options.usexhr) {
            alias.val(value).addClass('loading');
            // alias.parent().append('<div id="alias_result"></div>');
        } else {
            alias.val(value);

        }

        if (alias.val().length > 2) {
            $("#alias_result").hide();
            if (options.usexhr) {
                if (typeof xhr !== 'undefined')
                    xhr.abort();
                xhr = $.ajax({
                    url: common.url('/admin/app/ajax/check-slug'),
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
                            $("#alias_result").html('<span class="badge badge-danger">' + data.message + '</span>').show();
                        } else {
                             alias.parent().parent().addClass('has-success').removeClass('has-error');
                            // $("#alias_result").html('<span class="badge badge-success">' + data.message + '</span>').show();
                        }
                    }
                });
            }
        }
    });

}


