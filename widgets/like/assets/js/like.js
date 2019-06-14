$(function () {
    var xhr;

    $('a.like-down, a.like-up').click(function (e) {
        e.preventDefault();
        if (typeof xhr !== 'undefined') xhr.abort();
        var widget = $(this).data('widget');
        xhr = $.ajax({
            type: 'POST',
            dataType: 'json',
            url: $(this).attr('href'),
            data: {
                model: $(this).data('model')
            },
            success: function (data) {
                console.log(data, widget);
                $('.' + widget).removeClass('loading');
                $('.' + widget + ' .count-like').html(data.likeCount);
                $('.' + widget + ' .count-dislike').html(data.dislikeCount);

                if (data.active !== undefined) {
                    if (data.active) {
                        $('.' + widget + ' .like-up').addClass('active');
                        $('.' + widget + ' .like-down').removeClass('active');
                    } else {
                        $('.' + widget + ' .like-up').removeClass('active');
                        $('.' + widget + ' .like-down').addClass('active');
                    }
                } else {
                    $('.' + widget + ' .like-up').removeClass('active');
                    $('.' + widget + ' .like-down').removeClass('active');
                }
            },
            beforeSend: function () {
                $('.' + widget).addClass('loading');
            }
        });


        return false;

    });
    return false;
});
