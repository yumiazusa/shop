$(function(){
   //立即购买
   $("#buy_now").click(function(){
       if(_user ==""){
        alert("请先登录");
        return false;
       }
      var id =$('#proid').val();
      var ordernum =1;
      var versionId = $('.detail-con-change').find("input[name='attr_id2']").val();
      var colorId = $('.selected').find("input[name='attr_id1']").val();
       $.ajax({
          url: _url+"/buy_now",
          data:{'id':id,'ordernum':ordernum,"versionId":versionId,"colorId":colorId},
          type:"post",
          success:function(d){
            if(d ==1){
              window.location.href=_APP+"/MobileBuy/collect/buy/now";
            }else{
              alert('库存不足，请检查后再次添加');
            }
          }
       });
   })



})
