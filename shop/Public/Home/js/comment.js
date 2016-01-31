$(function(){ 
     $("textarea").focus(function(){ 
         $("#comment").html("");
     });



})



function submitComment(){ 
   var content = $("#ConsultContent").val();
   if(content ==""){ 
        $("#comment").html("内容不能为空");
        return false;
   }
   if(_user ==""){
       alert("登陆才能评论");
       return false;
   }
   var product_id = $("#product_id").val();
   var score = $("input:checked").val();
   $.ajax({ 
       url: _url+"/addComment",
       type:"post",
      
       data :{'score':score,'content':content,'product_id':product_id},
       success:function(d){ 
         if(d==1){
            alert("您已经评论过了");
          }else{
          $(".acomment").prepend(d);
         }

       }

   })
}
function allcomment(status,product_id){
     var str ="";
    $.ajax({
        url:_url+"/commentlist",
        type:"get",
        data:{'status':status,'id':product_id},
        dataType:"json",
      
       
        success:function(d){
          
           str +='<ul class="acomment">';
           $.each(d,function(i,ele){ 
               if(i!='page'){
                   str += '<li class="detail-pl">';
                   str += '<div class="detail-list">';
                   str += '<div class="user-precent-left display-inline-block">';
                   if(ele.thumb ==""){
                     str +='<img alt="头像" src="/Public/User/default.jpg" class="np-avatar">';
                   }else{
                    str += '<img alt="头像" src="'+ele.thumb+'" class="np-avatar">';
                   }
                   str += '<span>'+ele.username+'</span>';
                   str += ' </div>';
                   str += '<div class="display-inline-block detail-user-comm">';
                   str += '<div class="user-precent-header">';
                   str += '<span class="icon-star icon-star-'+ele.score+' display-inline-block"></span>';
                   str += '<span class="user-date" style="font-size:14px;">'+ele.comment_time+'</span>';
                   str += '</div>';
                   str += '<div class="user-precent-content" style="font-size:14px;"><p>'+ele.content+'</p></div>';
                   str += '</div>';
                   str += '</div>';
                   if(ele['replay']){
                      
                      str += '<div class="gl-response">管理员回复：';
                      str += '<span style="color:red;">'+ele['replay'].content +'</span>';
                      str += '<span style="color:red;">'+ele['replay'].replay_time+'</span>';
                      str += '</div>';
                   }
                   str +='</li>';
                }

            })
             str += '</ul>';
             str +='<div style="height:50px;margin-bottom:16px;">';
             str +=d.page;
             str +='</div>';  
             $("#pages").html(str); 
        }
    })
}