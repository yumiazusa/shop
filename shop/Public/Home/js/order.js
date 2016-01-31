$(function(){
	//售后服务详情的
	$(".bts").click(function(){
		var _this = $(this);
		if(_this.html()=="收起"){
			$("#up").slideUp();
			_this.html("展开");
		}else{
			$("#up").slideDown();
			_this.html("收起");
		}
	});
	$(".btns").click(function(){
		var _this = $(this);
		if(_this.html()=="收起"){
			$("#show").slideUp();
			_this.html("展开");
		}else{
			$("#show").slideDown();
			_this.html("收起");
		}
	})

	
	$("#provice").change(function(){
		//province();
		doChange($(this),"city");
	})
	$("#city").change(function(){
		//province();
		doChange($(this),"area");
	})
   
})
//省
function province(){
	$.ajax({
              url:_APP+"/city/provice",
              data:{"keyid":0},
              datatype:"json",
              type:"post",
             success:function(data){
                var str="";
             	var data=eval("("+data+")");
             	   $.each(data,function(i,n){
                       str +='<option value='+n.id+'>'+n.name+'</option>';
                    });                
                $("#provice").html(str); 
              } 
               
          });
}
//市县
function doChange(obj,name){
	var _id =$(obj).val();
      $.ajax({
			url:_APP+"/city/provice",
			data:{"keyid":_id},
			datatype:"json",
			type:"post",
			success:function(data){
				var str="";
				if(data!=null){
					var data=eval("("+data+")");
					if(name == "city"){
						str+="<option>市</option>";
					}else if(name == "area"){
						str+="<option>县/区</option>";
					}
					$.each(data,function(i,n){
						str +='<option value='+n.id+'>'+n.name+'</option>';
					});

				}
				$("#"+name).html("").append(str);
			}
		});
	}


//取消订单
function cancel(id){
    var _st = "#st"+id;

    var _status = "#status"+id;
    if(confirm("确定要取消吗?")){
	    $.ajax({
	    	  url : _url+"/cancel",
	    	  type : "post",
	    	  data:{'id':id},
	    	  success:function(d){
	             if(d ==1){
	             	$(_st).html("已取消");
	             	$(_status).html('<div style="margin-bottom:10px;text-align:center;"><a class="pad_r_10" href="javascript:void(0)" style="font-size:12px;">查看</a></div>');
	             }
	    	  }
	    })
	}
}

//取消服务单
function cancelService(obj){
	var id = obj;

	$.ajax({
		url:url+"/Custom/cancle",
		data:{'id':id},
		type:"post",
		success:function(d){
			if(d){
               $("#service").append(d);
               $("#cservice").html("服务单已经取消");
			}else{
				alert("取消失败");
			}
		}
	})
}

