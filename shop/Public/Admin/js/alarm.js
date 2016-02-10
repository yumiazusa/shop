function alarm(){
	$('#alarm').html('');
 	$('#alarm').html('<embed id="alarm" height="0" width="0" src="/Public/Admin/voice/1.mp3">');
 };

// alarm();

function checkmessage(){
	$.ajax({
                url: 'ajaxCheckmessage',
                type: "GET",
                dataType: "json",
                async : true,
                success: function(data){
                	if(data){
                		var feedback=parseInt(data.feedback);
                		var indexfeedback=parseInt(data.indexfeedback);
                		var productfeedback=parseInt(data.productfeedback);
                		var neworders=parseInt(data.orders);
                		var total=feedback+productfeedback+indexfeedback;
                		$('.messagetotal').html(total);
                		$('#feedbackspan').html(feedback);
                		$('#indexfeedbackspan').html(indexfeedback);
                		$('#productfeedbackspan').html(productfeedback);
                		$('#neworders').html(neworders);
                		alarm();
                	}else{
                		$('.messagetotal').html('');
                		$('#feedbackspan').html('');
                		$('#indexfeedbackspan').html('');
                		$('#productfeedbackspan').html('');
                		$('#neworders').html('');
                	}
                }
            });
}

checkmessage();

window.onload=function(){
	setInterval("checkmessage()",1000*60*60);
}