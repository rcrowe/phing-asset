$(document).ready(function(){
	
	//Lets handle rollovers over projects
	$("#projects article").mouseenter(function(){
		
		$(this).css('cursor', 'pointer');
		$(this).css('border-right', '7px solid #FF8800');
		
	}).mouseleave(function(){
		
		$(this).css('border-right', '7px solid #E3E1E9');
		
	});

});