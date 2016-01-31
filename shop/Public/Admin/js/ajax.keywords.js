$(function (){
    // -----------------------------------------------------------------------
    // 处理数量
    $('.edit').dblclick(function (){
        $(this).attr('contenteditable', true);
        $(this).css({'background': '#ffffff', 'border': '1px solid #cdcdcd'});
    });

    $('.edit').blur(function (){
        var data = $(this).text();
        var id   = $(this).attr('data-id');
        var obj  = this;
        $.ajax({
            'type': 'GET',
            'dataType': 'json',
            'data': {
                'times': data,
                'id': id
            },
            'success': function (d){
                if (d == 1)
                {
                    alert('修改成功');
                }
                else
                {
                    alert('对不起，修改失败');
                }
                $(obj).removeAttr('contenteditable');
                $(obj).css({'border':'none', 'background':'none'});
            },
            'url': saveTimesURL
        });
    });

    // --------------------------------------------------------------------
    // 处理关键字
    $('.kw').dblclick(function (){
        $(this).attr('contenteditable', true);
        $(this).css({'background': '#ffffff', 'border': '1px solid #cdcdcd'});
    });

    $('.kw').blur(function (){
        var id = $(this).attr('data-id');
        var txt = $(this).text();
        var obj = this;

        if (txt.length == 0)
        {
            alert('关键字不能为空');
            return false;
        }

        var data = {
            'id': id,
            'keyword': txt
        };
        $.post(saveKWURL, data, function (d){
            if (d == 1)
            {
                alert('修改成功');
            }
            else
            {
                alert('对不起，修改失败');
            }
            $(obj).removeAttr('contenteditable');
            $(obj).css({'border':'none', 'background':'none'});
        });
    });
});