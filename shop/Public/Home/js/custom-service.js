$(function(){
     $(".sidebox").hover(function(){
     	  $(this).stop().animate({width:"124px",background:"#A20303"},500);
     },function(){
     	    $(this).animate({width:"54px"},500);
     });

     $(".open").click(function(){
         window.open("online-custom.html","_blank","resizable=no,status=no,toolbar=no,menubar=no,location=no,width=510px,height=580px,top=100px,left=500px");
     });
})
function goTop(){
	$('html,body').animate({'scrollTop':0},600);
}



