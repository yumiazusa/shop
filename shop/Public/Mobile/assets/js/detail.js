

$('.color-pick').click(function(){
  var _this=$(this);
  if (!_this.hasClass("selected")) {

  	var _thisIndex = _this.index();
  		var _thisIndex = _this.index();
        var _thisPrice = data.color[_thisIndex];
        setPrice(_thisPrice);

        //获取颜色对应的轮播图片数组
        var _thisImages = data.images[_thisIndex];
        $(".swatches li.selected").removeClass("selected");
        $(this).addClass('selected');
        setImages(_thisImages);
        setPriceNumber();
  }
});


//设置与当前颜色相对应的版本价格
    function setPrice(priceObject) {
        var priceHtml = "";
        var checkPrice = "";
        var number = 0;
        var price = eval($('#promote_price').val());
        var rate = eval($('#promote').val());
        var colorPrice = eval(priceObject.prop_price);
        var versionprice = eval($('.detail-con-change').find("input[name='upprice2']").val());
        if(rate){
            rate = rate/100;
            checkPrice = (colorPrice + versionprice + price)*rate;
        }else{
            checkPrice = colorPrice + versionprice + price;
        }
        $('.price-sales').find('.newer').html(checkPrice.toFixed(2));
    }


//设置轮播图片的HTML结构，并赋值
    function setImages(imageObject) {
        var imageHtml = "";
        var thumbHtml =""
        if (imageObject != undefined && imageObject != null && imageObject.length > 0) {
            $.each(imageObject, function (index, ele) {
                if (index == 0) {
                    imageHtml += '<a href="' + ele + '" class="img-responsive" alt="img"><img src="' + ele + '" class="img-responsive" alt="img"></a>';
                    thumbHtml += '<a href="' + ele + '" class="sp-current"><img src="' + ele + '" class="img-responsive" alt="img"></a>';
                } else {
                    thumbHtml += '<a href="' + ele + '"><img src="' + ele + '" class="img-responsive" alt="img"></a>';
                }
            });
        }
        $('.main-image').find('.sp-large').html(imageHtml);
        $('.main-image').find('.sp-thumbs').html(thumbHtml);
    }




//切换规格
$('.size-pick').click(function(){
  var _this=$(this);
  var checkPrice2 ="";
  $('.size-pick').attr('class','size-pick detail-con-vsion');
  if (!_this.hasClass("detail-con-change")) {
            // //获取当前选中版本的索引
            var _thisIndex = _this.parent().index();
            // //获取当前选中颜色的索引
            var colorCheckIndex = $('.selected').index();
            // //获取当前选中颜色及版本对应的数据
            var colorprice = eval(data.color[colorCheckIndex]['prop_price']);
            var rate = eval($('#promote').val());
            var versionprice =eval(data.price[_thisIndex]['prop_price']);
            var price = eval($('#promote_price').val());
            if(rate){
            rate = rate/100;
            checkPrice2 = (colorprice + versionprice + price)*rate;
            }else{
            checkPrice2 = colorprice + versionprice + price;
            }
            $('.price-sales').find('.newer').html(checkPrice2.toFixed(2));
            _this.addClass("detail-con-change");
            setPriceNumber();
        }
})


//设置库存
    function setPriceNumber() {
        var versionId = $('.detail-con-change').find("input[name='attr_id2']").val();
        var colorId = $('.selected').find("input[name='attr_id1']").val();
        var id =$('#proid').val();
        $.ajax({
            url: _url+'/ajaxGetNum',    //请求的url地址
            dataType: "json",   //返回格式为json
            async: true, //请求是否异步，默认为异步，这也是ajax重要特性
            data: { "id": id,'colorId':colorId,'versionId':versionId },    //参数值
            type: "POST",   //请求方式
            success: function(data) {
            var storenum =$('.storeNum');
            var num = parseInt(data)
            storenum.html(num);
            if(num >0){
                $('.cart').css('display','none');
                $('.wishlist').css('display','none');
                $('.cart').css('display','block');
            }else{
                $('.cart').css('display','none');
                $('.wishlist').css('display','none');
                $('.wishlist').css('display','block');
            }
            }
         });
    }


function goods(){
        setPriceNumber();
    }