var stacks = {0:"left",1:"middle",2:"right"};
$(document).ready(function() {
	$.ajax({
		url: "/pollution/story.php?page="+1,
		dataType: "json",
		context: document.body
	}).done(function(data) {
    	for (i in data) {
	    	$("#sky-"+data[i].stack).append("<div id='cloud'>"+data[i].html+"<div id='vote' data-article='"+data[i].id+"'><div class='vote' data-stack='-1' id='left-vote'>&larr;</div><div class='vote' data-stack='0' id='middle-vote'>&uarr;</div><div class='vote' data-stack='1' id='right-vote'>&rarr;</div></div></div>");
	    	for (k in stacks) {
        		if (stacks[k] != data[i].stack) {
	        		$("#sky-"+stacks[k]).append("<div id='nocloud' class='nocloud-"+data[i].id+"'>&nbsp;</div>"); 
	        	}
	        }
        }
        
        $(".vote").hover(function() {
	        $(this).css("background-color","#EEE");
	        
        }, function() {
	        $(this).css("background-color","#FFF");
        }).click(function() {
        	$.ajax({
	        	url: "/pollution/vote.php?article="+$(this).parent().data('article')+"&stack="+$(this).data('stack'),
	        	dataType: "text",
	        	context: this
        	}).done(function(data) {
        		$("#sky-"+data).prepend($(this).parent().parent());
        		$(".nocloud-"+$(this).parent().data('article')).hide();
        		for (k in stacks) {
 	     	  		if (stacks[k] != data) {
	        			$("#sky-"+stacks[k]).prepend("<div id='nocloud' class='nocloud-"+$(this).parent().data('article')+"'>&nbsp;</div>"); 
	        		}
	        	}
	        });
        }); 
        
	});
});

