$(function (){
    // 底部按钮用于不同产品之间的切换
    $('.scroll-btns-side a').each(function (i) {

        $(this).click(function (){
            $(this).addClass('active').siblings('a').removeClass('active');
            // 1、对应的是哪个UL
            var oPrevUL = $('.scroll-image-box ul').eq(i);
            // 将对应的UL显示，其它的隐藏
            oPrevUL.show().siblings('ul').hide();
            
            // 2、这个UL下有多少个LI
            var oLi = oPrevUL.children('li');
            var iLiCounts = oLi.length; 
            // 当前页
            var nowPage = oPrevUL.attr('nowPage') ? oPrevUL.attr('nowPage') : 1;
            nowPage = parseInt(nowPage);
            // 3、默认显示前四个图片到前面
            // 4、LI个数大于四个，要显示右侧的按钮
            // var nextPage = nowPage + 1;
            // if (nextPage*4 >= iLiLength){
            //  $('a.next').hide();
            // }
            // 将所有的LI放回去
            oLi.css('left', '1100px');
            if (iLiCounts > 4)
            {
                // 默认显示头四个，然后显示下一页的按钮
                for(var n = (nowPage-1)*4; n < nowPage * 4; n++)
                {
                    var position = n%4 * 250 + 'px';
                    var timeAdd = n%4 * 100 + 300;
                    $(oLi[n]).animate({'left':position}, timeAdd);
                }
                // oPrevUL.attr('nowPage', nowPage + 1);
                // 显示下一页的按钮
                $('a.next').show();

            }
            else
            {
                for (var m=0; m<iLiCounts; m++)
                {
                    var position = m * 250 + 'px';
                    var timeAdd = m * 100 + 300;
                    $(oLi[m]).animate({'left':position}, timeAdd);
                }
                $('a.prev').hide();
                $('a.next').hide();
            }
            // 5、为右侧按钮添加点击事件
            //      |- 先将原有的移除，再将没插入的移过来，并添加左侧的按钮
            //      |- 判断图片个数有没有大于八个，大于继续显示右侧按钮，否则隐藏
        });
        $('.scroll-btns-side a:first').click();
    });

    // 上一页
    $('a.prev').click(function (){
        // 获取当前页，先将现在有的全部移到右边
        var oPrevUL = $('.scroll-image-box ul:visible');
        var oLi = oPrevUL.children('li');
        var iLiLength = oLi.length;
        var nowPage = oPrevUL.attr('nowPage');

        for(var n = nowPage*4-1; n >= (nowPage-1) * 4; n--)
        {
            var position = '1100px';
            var timeAdd = 600 - n%4 * 100 ;
            $(oLi[n]).animate({'left':position}, timeAdd);
        }
        // 显示右侧的按钮
        $('a.next').show();
        // 将左侧的图片全部移入
        prevPage = nowPage - 1;
        for(var m = prevPage * 4 - 1; m >= (prevPage-1) * 4; m--)
        {
            var position = m%4 * 250 + 'px';
            var timeAdd = m%4 * 100 + 300;
            $(oLi[m]).animate({'left':position}, timeAdd);
        }
        // 判断左侧是否还有图片，若没有了则隐藏左侧按钮
        if (prevPage <= 1)
        {
            $('a.prev').hide();
        }
        oPrevUL.attr('nowPage', prevPage);
    });

    // 下一页
    $('a.next').click(function (){
        // 获取当前页，将当前页的LI全部移到左边
        // 当前页是visible
        var oPrevUL = $('.scroll-image-box ul:visible');
        var oLi = oPrevUL.children('li');
        var iLiLength = oLi.length;
        var nowPage =parseInt(oPrevUL.attr('nowPage'));
        for(var n = (nowPage - 1)*4; n < nowPage * 4; n++)
        {
            var position = '-250px';
            var timeAdd = n%4 * 100 + 300;
            $(oLi[n]).animate({'left':position}, timeAdd);
        }
        // 显示左边的跳转按钮
        $('a.prev').show();
        // 将下一页的移过来，看还有没有更多，没有了则不显示下一页了
        var nextPage = nowPage + 1;
        if (nextPage*4 >= iLiLength){
            $('a.next').hide();
        }
        for(var m = (nextPage - 1)*4; m < nextPage * 4; m++)
        {
            var position = m%4 * 250 + 'px';
            var timeAdd = m%4 * 100 + 300;
            $(oLi[m]).animate({'left':position}, timeAdd);
        }
        $(oPrevUL).attr('nowPage', nextPage);
    });

    // 处理服务与支持首页区块特效
    $('.image1').hover(function (){
        $(this).animate({'opacity':'0'}, 1000);
    }, function (){
        $(this).animate({'opacity':'1'}, 1000);
    });

});