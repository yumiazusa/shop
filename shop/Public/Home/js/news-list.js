$(function (){
    $('.r-top-title').each(function (i){
        $(this).click(function () {
            $(this).addClass('active').siblings().removeClass('active');
            $('.top-news').eq(i).show().siblings('.top-news').hide();
        });
    });
});