$(function () {
    var xhr;
    $(document).on('click','a.like-down, a.like-up',function (e) {
        e.preventDefault();
        if (typeof xhr !== 'undefined') xhr.abort();
        var widget = $(this).data('widget');
        xhr = $.ajax({
            type: 'POST',
            dataType: 'json',
            url: $(this).attr('href'),
            data: {
                handler_hash: $(this).data('hash')
            },
            success: function (data) {
                $('.' + widget).removeClass('loading');
                $('.' + widget + ' .count-like').html(data.likeCount);
                $('.' + widget + ' .count-dislike').html(data.dislikeCount);

                if (data.active !== undefined) {
                    if (data.active) {
                        $('.' + widget + ' .like-up').parent().addClass('active');
                        $('.' + widget + ' .like-down').parent().removeClass('active');
                    } else {
                        $('.' + widget + ' .like-up').parent().removeClass('active');
                        $('.' + widget + ' .like-down').parent().addClass('active');
                    }
                } else {
                    $('.' + widget + ' .like-up').parent().removeClass('active');
                    $('.' + widget + ' .like-down').parent().removeClass('active');
                }
            },
            beforeSend: function () {
                $('.' + widget).addClass('loading');
            },
            error: function(jsxhr , textStatus, errorThrown ){
                $('.' + widget).removeClass('loading');
            }
        });
        return false;
    });
    return false;
});
