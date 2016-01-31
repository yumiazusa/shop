$(function (){
	// 滑动菜单开始
	var timer = null;
	$('#menu-product').hover(function (){
		clearTimeout(timer);
		$('.slide-menu').slideDown(300);
	}, function (){
		timer = setTimeout(function (){
			$('.slide-menu').slideUp(300);
		}, 500);
	});

	$('.slide-menu').hover(function (){
		clearTimeout(timer);
		$('.slide-menu').slideDown(300);
		$('.menu li a').removeClass('active');
		$('.menu li a:eq(1)').addClass('active');
	}, function (){
		timer = setTimeout(function (){
			$('.slide-menu').slideUp(300);
			$('.menu li a').removeClass('active');
			$('.menu li a:eq(0)').addClass('active');
		}, 500);
	});
	// 滑动菜单结束
	
	$('.menu li a:gt(0)').hover(function (){
		$('.menu li a:first-child').removeClass('active');
		$(this).addClass('active');
	}, function (){
		$('.menu li a:first-child').addClass('active');
		$('.menu li a:gt(0)').removeClass('active');
	});

	// 搜索开始
	$('.btn-search').click(function (){
		if ($('.search-box').css('display') == 'none')
		{
			$('.search-box').slideDown(600);
		}
		else
		{
			$('.search-box').slideUp(600);		
		}
	});

	$('.search-box').hover(function (){
		$('#close-search-box').css('display', 'block');
	}, function (){
		$('#close-search-box').css('display', 'none');
	});

	$('#close-search-box').click(function (){
		$('.search-box').slideUp(600);
	});
	// 搜索结束

	// 顶部购物车
	var cartTimer = null;
	$('.btn-cart').hover(function (){
		clearTimeout(cartTimer);
		$('.cart-top').slideDown(300);
	}, function (){
		cartTimer = setTimeout(function (){
			$('.cart-top').slideUp(300);
		}, 300);
	});


	// 平板产品
	$('.tablet-product').hover(function (){
		$(this).find('.tablet-line-nav span').animate({'width':'100%'}, 500);
	}, function (){
		$(this).find('.tablet-line-nav span').animate({'width':'60px'}, 500);
	});

	// 穿戴产品
	$('.wearable .wearable-image-small div:lt(2)').hover(function (){
		$('.wearable .wearable-image-small div').finish();
		$(this).parent().css({'overflow':'visible', 'z-index':10});
		$(this).css('border-bottom', '1px solid #fff');
		$(this).animate({'height':'350px'}, 400);
	}, function (){
		$('this').stop();
		$(this).animate({'height':'275px'}, 400, function (){
			$(this).parent().css({'overflow':'hidden', 'z-index':1});
			$(this).css('border-bottom', 'none');
		});
	});

	// 穿戴产品????
	$('.wearable .wearable-image-small div:gt(1)').hover(function (){
		$('.wearable .wearable-image-small div').finish();
		$(this).parent().css({'overflow':'visible', 'z-index':10});
		$(this).css('border-top', '1px solid #fff');
		$(this).animate({'top':'-75px'}, 400);
	}, function (){
		$('this').stop();
		$(this).animate({'top':'0px'}, 400, function (){
			$(this).parent().css({'overflow':'hidden', 'z-index':1});
			$(this).css('border-top', 'none');
		});
	});

	// 配件
	$('.accessories td:not(.band)').hover(function (){
		$('.accessories td:not(.band)').not(this).css('opacity', '0.5');
	}, function (){
		$('.accessories td:not(.band)').css('opacity', '1');
	});


	// 手机
	// $(".mobile-image-td").each(function (){
	// 	$(this).hover(function(){
	// 		$(this).find('.mobile-desc').animate({bottom:'0'}, 600);
	// 	}, function (){
	// 		if ($(this).find('.mobile-desc').attr('flag-shot') == '1')
	// 		{
	// 			$(this).find('.mobile-desc').animate({bottom:'-50%'}, 600);
	// 		}
	// 		else
	// 		{
	// 			$(this).find('.mobile-desc').animate({bottom:'-25%'}, 600);
	// 		}
	// 	});
	// });
	$(".mobile-desc").each(function (){
		$(this).hover(function(){
			$(this).animate({bottom:'0'}, 600);
		}, function (){
			if ($(this).attr('flag-shot') == '1')
			{
				$(this).animate({bottom:'-50%'}, 600);
			}
			else
			{
				$(this).animate({bottom:'-25%'}, 600);
			}
		});
	});
});
