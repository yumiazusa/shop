window.onload=function(){
    var url= window.location.href;
    url=url.substr(7);
    var num= url.indexOf("/");
    url=url.substr(num+1);
    var num2= url.indexOf("/");
    url=url.substr(num2+1);
    var num3 = url.indexOf("/");
    url=url.substr(0,num3);
    $('.menua').each(function(){
    	var href =$(this).attr('href');
    	href=href.substr(1);
    	var num4=href.indexOf('/');
    	href=href.substr(num4+1);
    	var num5=href.indexOf('/');
    	href=href.substr(0,num5);
    	if(href == url){
    		$('#indexmune').attr('class','');
    		$(this).parent().parent().parent().attr('class','submenu open active');
    		// $(this).parent().css('background','#91AAF3');
    	}
    });
}