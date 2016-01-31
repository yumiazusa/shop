$(function(){
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

