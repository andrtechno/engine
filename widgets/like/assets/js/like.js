$(function(){
    var xhr;

    $('a.like-down, a.like-up').click(function(e){
        e.preventDefault();
        if(typeof xhr !== 'undefined') xhr.abort();
        var widget = $(this).attr('data-widget-id');
        xhr = $.ajax({
            type:'POST',
            dataType:'json',
            url:$(this).attr('href'),
            data:{
                token:$(this).attr('data-csrf'),
            },
            success:function(data){
                //  console.log(data); 
                $('#'+widget).removeClass('loading'); 
                $('#'+widget+' .like-counter').html(data.num);
            },
            beforeSend:function(){
               $('#'+widget).addClass('loading'); 
            }
        });
        console.log(xhr);

        return false;

    });
    return false;
})
