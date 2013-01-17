<html>
<head>
<title>Pollution</title>
<style>
body {
	font-family:"Courier New", Courier, monospace;
	font-size:12px;
	background-color:#c0c0c0;
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
	background-image: url(stacks.png);
	position:fixed;
	bottom:0px;
	left:50%;
	width:800px;
	height:200px;
	margin-left:-400px;
}

#link {
	margin-top:100px;
	height:25px;
	width:400px;
	font-size: 16px;
}

#vote {
	font-size:18px;
	text-align:center;
	vertical-align:bottom;
	height:50px;
}

#left-vote {
	text-align:left;
	display:inline;
}

#middle-vote {
	text-align:center;
	display:inline;
}

#right-vote {
	text-align:right;
	display:inline;
}



</style>
<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
<script>

var FB;

window.fbAsyncInit = function() {
	FB.init({
		appId      : '487190331324891', // App ID
		channelUrl : 'http://pol.lutio.net/channel.html', // Channel File
		status     : true, // check login status
		cookie     : true, // enable cookies to allow the server to access the session
		xfbml      : true  // parse XFBML
    });
    
    

};

  // Load the SDK Asynchronously
(function(d){
	var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement('script'); js.id = id; js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    ref.parentNode.insertBefore(js, ref);
}(document));


function load(page) {
	$.ajax({
		url: "/story.php?page="+page,
		dataType: "json",
		context: document.body
	}).done(function(data) {
    	for (i in data) {
    		if (data[i].html != null)
    		$("#sky").append("<div id='row' class='"+data[i].stack+"'><div id='cloud' class='cloud-"+i+"'>"+data[i].html+"</div></div>");		
		}
		$(".vote").click(function(e) {
			var voteButtons = $(this).parent();
			var cloud = $(this).parent().parent();
			var stack = $(this).data('stack');
			var articleID = $(this).parent().data('article');
			e.preventDefault();
			FB.getLoginStatus(function(login_response) {
				if (login_response.status === 'connected') {
					FB.api('/me',function(graph_response) {
						$.ajax({
							url: "/story.php?do=vote&article="+articleID+"&stack="+stack+"&fbuid="+graph_response.id,
							context: this
						}).complete(function(vote_response) {
							if (vote_response.responseText == 'dupe') {
								alert("dupe!");
							} 
							else if (vote_response.responseText == 'error') {
								alert("error3");
							}
							else {
								alert(vote_response.responseText);
								$("#sky").prepend(cloud.parent().attr('class',vote_response.responseText));
								voteButtons.hide();
							}
						});
					});				
				} else if (login_response.status === 'not_authorized') {
					login();
				} else {
					login();
				}
			});
		});
		
		$("#sky").append($("#more"));
    });
    
}

function login() {
    FB.login(function(response) {
        if (response.authResponse) {
            alert("yay!");
        } else {
            console.log(response);
        }
    });
}

function submit(value,stack) {
	var regex = new RegExp("^(http[s]?:\\/\\/(www\\.)?|ftp:\\/\\/(www\\.)?|www\\.){1}([0-9A-Za-z-\\.@:%_\+~#=]+)+((\\.[a-zA-Z]{2,3})+)(/(.)*)?(\\?(.)*)?");
	if(value.match(regex)) {
		FB.getLoginStatus(function(login_response) {
			if (login_response.status === 'connected') {
				FB.api('/me',function(graph_response) {
					$.ajax({ 
						url: '/story.php?do=post&stack='+stack+'&fbuid='+graph_response.id+'&url='+encodeURIComponent(value)
					}).complete(function(response) {
						if (response.responseText == 'success') {
							var more = $("#more");
							$("#sky").empty();
							load(1);
							$("#sky").append(more);
						} else if (response.responseText == 'no data') {
							alert("We can't find this article's info.");
						} else if (response.responseText == 'dupe') {
							alert("We already know about that article!");
						} else if (response.responseText == 'bad') {
							alert("That's not a link...");
						} else if (response.responseText == 'fail') {
							alert("fail");
						}
					});
				});
			} else if (login_response.status === 'not_authorized') {
				login();
			} else {	
				login();
			}
		});
	}
	else alert("That's not a link....");
}
$(document).ready(function() { 
    load(1);
    $("#more").click(function() {
		load($(this).data('page'));
		$(this).data('page',$(this).data('page')+1);
	}); 
	$("#link").click(function() {
		$(this).val("");
	})
	$("#stack > button").each(function() {
		$(this).click(function(e) {
			e.preventDefault();
			var value = $("#link").val();
			$("#link").val("Processing your link...");
			submit(value,$(this).val());
			$("#link").val("Submit a link to fuel the fire!");
		});
	});
});
</script>
</head>
<body>
<div id="fb-root"></div>
<div id="frame">
  <div id="top"><h1>Welcome to Pollutio.net</h1><h2>Politics is polluting the internet with bias. Help us sort it out.</h2><h3>Scroll to see which stories are trending, sorted by their perceived political bias.</h3><h4>Connect through Facebook to help rate these articles!</div>
  <div id="sky"><div id="more" data-page="1">More</div></div>
  <div id="stack">
  	<input id="link" value="Submit a link here to fuel the fire!"><br>
  	<button value="-1">Left</button>
  	<button value="0">Middle</button>
  	<button value="1">Right</button>
  </div>
</div>
</body>
</html>