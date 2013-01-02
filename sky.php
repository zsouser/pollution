<html>
<head>
<title>Pollution</title>
<style>
body {
	font-family:"Courier New", Courier, monospace;
	font-size:12px;
	background-color:#06c8f9
}
#top {
	height:100%;
	padding-top:100px;
}
#frame {
	text-align:center;
}

#row {
	margin-bottom:30px;
}

#cloud {
	vertical-align: middle;
	text-align:center;
	padding:25px;
	border:1px solid black;
}
.right {
	padding-left:550px;
	width:250px;
}

.left {
	padding-right:550px;
	width:250px;
}

.middle {
	width:300px;
	padding-left:250px;
}

#sky {
	position:absolute;
	left:50%;
	width:800px;
	margin-left:-400px;
}

#more {
	position:absolute;
	height:350px;
	width:300px;
	left:50%;
	margin-left:-150px;
	height:50px;
	vertical-align: top;
	text-align: center;
	padding-bottom:350px;
}
#stack {
	position:fixed;
	bottom:0px;
	left:50%;
	width:800px;
	height:200px;
	margin-left:-400px;
}

#vote {
	font-size:18px;
	text-align:center;
	vertical-align:bottom;
	height:50px;
}

#left-vote {
	font-size:18px;
	text-align:left;
	display:inline;
	padding:10px;
}

#middle-vote {
	text-align:center;
	display:inline;
	padding:10px;
}

#right-vote {
	text-align:right;
	display:inline;
	padding:10px;
}

#left-stack {
	position:absolute;
	width:250px;
	background-color:blue;
	height:250px;
	vertical-align: middle;
}

#middle-stack {
	position:absolute;
	left:250px;
	bottom:0px;
	width:300px;
	background-color:#ccc;
	height:300px;
	vertical-align: middle;
}

#right-stack {
	position:absolute;
	left:550px;
	width:250px;
	background-color:red;
	height:250px;
	vertical-align: middle;
}

</style>
<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
<script>

function more(page) {
	
}
function load(page) {
	$.ajax({
		url: "/pollution/story.php?page="+page,
		dataType: "json",
		context: document.body
	}).done(function(data) {
    	for (i in data) {
    		$("#sky").append("<div id='row' class='"+data[i].stack+"'><div id='cloud'>"+data[i].html+"</div></div>");
        }
                
        $(".vote").hover(function() {
	        $(this).css("color","#222");
        }, function() {
	        $(this).css("color","#000");
        }).click(function() {
        	$.ajax({
	        	url: "/pollution/vote.php?article="+$(this).parent().data('article')+"&stack="+$(this).data('stack'),
	        	context: this
        	}).complete(function(data) {
        		$("#sky").prepend($(this).parent().parent().parent().attr('class',data.responseText));
	        });
        });
        $("#sky").append($("#more"));
              
	});
}
$(document).ready(function() {
	load(1); 
    $("#more").click(function() {
		load($(this).data('page'));
		$(this).data('page',$(this).data('page')+1);
	}); 
});
</script>
</head>
<body>
<div id="frame">
  <div id="top"><h1>Welcome to Pollutio.net</h1><h2>Politics is polluting the internet with bias. Help us sort it out.</h2><h3>Scroll to see which stories are trending, sorted by their perceived political bias.</h3><h4>Connect through Facebook to help rate these articles!</div>
  <div id="sky"><div id="more" data-page="1">More</div></div>
  <div id="stack">
	  <div id="left-stack"></div>
	  <div id="middle-stack"></div>
	  <div id="right-stack"></div>
  </div>
</div>
</body>
</html>