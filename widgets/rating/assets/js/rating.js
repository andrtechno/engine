function ajax_rating(id){
    var url = '/shop/ajax/rating/'+id;
    var rating = $('input[name=rating_'+id+']:checked').val();
    $('input[name=rating_'+id+']').rating('disable');
    $.ajax({
        url: url,
        type:'POST',
        dataType:'json',
        data:{rating:rating},
        success:function(data){
           $('input[name=rating_'+data.result+']:checked').val();
        }
    });
}