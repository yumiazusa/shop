$(function(){
	//加入购物车
   $("#addBtn").click(function(){
   	   var color = $(".detail-con-ys-change .detail-con-yanse").attr("title");
   	   var version    = $(".detail-con-change .detail-con-mon").html();
       // var price = $(".price .newer").html();
       var id =$('#proid').val();
       var ordernum =parseInt($('#num').val());
       var versionId = $('.detail-con-change').find("input[name='attr_id2']").val();
       var colorId = $('.detail-con-ys-change').find("input[name='attr_id1']").val();
       $.ajax({
          url: _url+"/cart",
          data:{'id':id,'ordernum':ordernum,'version':version,'color':color,"versionId":versionId,"colorId":colorId},
          type:"post",
          success:function(d){
          	if(d ==1){
          		window.location.href=_APP+"/Buy/cart";
          	}else{
              alert('库存不足，请检查后再次添加');
            }
          }
       });
   })

   //立即购买
   $("#buy_now").click(function(){
       if(_user ==""){
        alert("请先登录");
        return false;
       }
      var color = $(".detail-con-ys-change .detail-con-yanse").attr("title");
      var version    = $(".detail-con-change .detail-con-mon").html();
      var id =$('#proid').val();
      var ordernum =parseInt($('#num').val());
      var versionId = $('.detail-con-change').find("input[name='attr_id2']").val();
      var colorId = $('.detail-con-ys-change').find("input[name='attr_id1']").val();
       $.ajax({
          url: _url+"/buy_now",
          data:{'id':id,'ordernum':ordernum,'version':version,'color':color,"versionId":versionId,"colorId":colorId},
          type:"post",
          success:function(d){
            if(d ==1){
              window.location.href=_APP+"/Buy/collect/buy/now";
            }else{
              alert('库存不足，请检查后再次添加');
            }
          }
       });
   })



})
