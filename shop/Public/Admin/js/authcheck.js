window.onload=function(){
            $('#collapseTwo').addClass('in');
            $('#collapse1').addClass('in');
            $('#collapseOne').removeClass('in');
            $('#myTab a:first').tab('show');
            //全选或反选操作
            $(".moduleName").click(function(){
                var index=$(this).attr('index');
                //全选操作
                if(this.checked){
                    //初始化len为空
                    var len=0;
                    //循环选择权限操作
                    $("input[label='group"+index+"']").each(function(i){
                        len++;                  
                        this.checked=true;
                    });
                    $(this).attr('len',len)
                //反选操作
                }else{
                    $("input[label='group"+index+"']").removeProp("checked");
                    $(this).attr("len",'0');
                }
            
            });

            //页面加载时运行时,显示对应角色的权限
            var ruleID=$('#groupID').data('rule');
            var patt = new RegExp(',');
            if(patt.test(ruleID)){//字符串存在返回true否则返回false
                var arr=ruleID.split(',');
                    }else{
                var arr = ruleID;
                    }
            //循环判断,有权限就选择
            $("input[name='rule[]']").each(function(){  
                //判断是否在数组中      
                if($.inArray(this.value,arr)>=0 || $(this).val() == arr){
                    $(this).prop('checked','true');
                    //获取当前label标签的最后一个数字,通过这个数字来获取相应的模块组
                    var label=$(this).attr('label').charAt(5);
                    //权限所在模块组
                    var grp=$('input[index="'+label+'"]');
                    //len属性默认为0,对len循环加1操作
                    var len=parseInt(grp.attr('len'))+1;
                    //改变len的值 
                    grp.attr('len',len);
                    //判断这模块组是否已经选择,如没有选择,则选上
                    if(!grp.prop('checked')){
                        grp.prop('checked','true');
                    }
                }
            });

            //选择权限时,判断该权限所在的模块组是否选上
            $("input[name='rule[]']").click(function(){
                //获取当前label标签的最后一个数字,通过这个数字来获取相应的模块组
                var label=$(this).attr('label').charAt(5);
                //权限所在模块组
                var grp=$('input[index="'+label+'"]');
                //所在模块组已经选中的权限个数
                var len=parseInt(grp.attr('len'));
                if(this.checked){
                    //每选中一次,执行自加操作  
                    len++;
                    grp.attr('len',len);
                    if(!grp.prop('checked')){
                        //判断这模块组是否已经选择,如没有选择,则选上
                        grp.prop('checked','true');
                    }                   
                }else{
                    //每取消一次,执行自减操作
                    len--;
                    grp.attr('len',len);
                    //当被选择权限数为0时,移除模块组选择状态
                    len<=0?grp.removeProp('checked'):'';                
                }               
            });

        }
