$(function(){
  
    $("#url").focus(function(){
           $(this).next().hide();
   });
   $("#url").blur(function(){
            $(this).next().show();
            //yzUrl();
    });
    // $("input:checked").click(function(){
    //     alert(111);
    // })

}) 

    // function yzUrl(){
    //    var url=/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
    //     var isUrl =false;
    //     var _this = $("#url");
    //        if(_this.val().match(url)==null){
    //              _this.next().html("请输入正确的url");
    //          }else{
    //             _this.next().html("");
    //             isUrl = true;
    //       }
    //     return isUrl;
    //  }
   function dosubmit(){
       var isUrl = yzUrl();
       if(isUrl){
          return true;
       }else{
          return false;
       }
   }
   function del(){
       var all = new Array();
       for(var i=0;i<$(".aa").length;i++){
          all[i]=($(".aa").eq(i).val());
        }
        //alert(all);
       if(confirm("确定要删除吗?")){
          $.ajax({
             url:_URL_+"/delall",
             type:"get",
             data:{id:all},
             dataType:"json",
             success:function(data){
                if(data==1){
                     window.location.replace(_URL_+"/catelist");
                }
             }
        });
      }
   }
      