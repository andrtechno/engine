$(function () {

    var cook_name = 'webpanel';
    var obj = 'webpanel';

    if ($.cookie(cook_name) === 'true') {
        $('.' + obj).addClass(obj + '-close');
        $('.' + obj + ' .' + obj + '-navbar-nav, .' + obj + ' .' + obj + '-navbar-toggle').addClass('hidden');
    } else {
        $('.' + obj).removeClass(obj + '-close');
        $('.' + obj + ' .' + obj + '-navbar-nav, .' + obj + ' .' + obj + '-navbar-toggle').removeClass('hidden');
    }

    $('.' + obj + '-logo').click(function () {
        $('.' + obj).toggleClass(obj + '-close');
        $('.' + obj + ' .' + obj + '-navbar-nav, .' + obj + ' .' + obj + '-navbar-toggle').toggleClass('hidden');
        $.cookie(cook_name, $('.' + obj).hasClass(obj + '-close'), {expires: 7});
    });

});



