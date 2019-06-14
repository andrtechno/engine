$(function () {
    var xhr;

    $('a.like-down, a.like-up').click(function (e) {
        e.preventDefault();
        if (typeof xhr !== 'undefined') xhr.abort();
        var widget = $(this).data('widget');
        xhr = $.ajax({
            type: 'POST',
             dataType:'json',
            url: $(this).attr('href'),
            data: {
                 model:$(this).data('model'),
            },
            success: function (data) {
                 console.log(data,widget);
                $('.' + widget).removeClass('loading');
                $('.' + widget + ' .count-like').html(data.likeCount);
                $('.' + widget + ' .count-dislike').html(data.dislikeCount);
            },
            beforeSend: function () {
                $('.' + widget).addClass('loading');
            }
        });
        console.log(xhr);

        return false;

    });
    return false;
});
