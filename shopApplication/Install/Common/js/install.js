$(function (){

    // 读写权限检查
    $('#next-step2').click(function (){
        var dirLength = $('.check-dir').length;
        var isReadableLength = $('span[is-read="1"]').length;
        var isWriteableLength = $('span[is-write="1"]').length;

        if (isReadableLength < dirLength || isWriteableLength < dirLength)
        {
            alert('对不起，请检查目录读写权限.');
            return false;
        }
        else
        {
            location.href = url;
        }
    });
});