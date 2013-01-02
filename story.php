<?php


mysql_connect("localhost","zach","password");	
mysql_select_db("test");


if (isset($_GET['page'])) {
	$q = "
		SELECT articles.*, avg(rankings.rank) as rank
		FROM articles
		LEFT JOIN rankings ON articles.id = rankings.article_id 
		GROUP BY rankings.article_id
		ORDER BY articles.date_updated DESC
		LIMIT ".(($_GET['page']-1)*20).", 20";
		$query = mysql_query($q);
		$arr = array();
		while ($result = mysql_fetch_object($query)) {
			$stack = "";
			if ($result->rank > 0.125) $stack = "right";
			else if ($result->rank < -0.125) $stack = "left";
			else $stack = "middle";
			$html = "
			<div id='inner'>
				<b><a href=\"".$result->url."\">".$result->title."</a></b><br>
				<p>".$result->description."</p>
			</div>
			<div id='vote' data-article='".$result->id."'>
				<div class='vote' data-stack='-1' id='left-vote'>&larr;</div>
				<div class='vote' data-stack='0' id='middle-vote'>&uarr;</div>
				<div class='vote' data-stack='1' id='right-vote'>&rarr;</div>
			</div>
			";
			$arr[] = array("id"=>$result->id,"stack"=>$stack,"html"=>$html);
			
	}
	header("Content-Type:text/javascript");
	echo json_encode($arr);
	die;
}

else if ($_GET['do'] == 'vote') {
	$q = "
		INSERT INTO rankings
		(article_id,rank) 
		VALUES 
		('".mysql_real_escape_string($_GET['article'])."','".mysql_real_escape_string($_GET['stack'])."')";
	$query = mysql_query($q);
	if ($query) {
		$query1 = mysql_query("update articles set date_updated = NULL where id = '".$_GET['article']."'");
		echo mysql_error();
		$query2 = mysql_query("select avg(rank) as r from rankings where article_id = '".$_GET['article']."' group by article_id");
		$result = mysql_fetch_object($query2);
		if ($result->r > 0.125)  echo "right";
		else if ($result->r < -0.125) echo "left";
		else echo "middle";
	} else {
		// log error
	}
	
}