<?php


mysql_connect("localhost","zach","password");	
mysql_select_db("test");


if (isset($_GET['page'])) {
	$query = mysql_query("SELECT articles.*, avg(rankings.rank) as rank
							FROM articles
							LEFT JOIN rankings ON articles.id = rankings.article_id 
							GROUP BY rankings.article_id
							ORDER BY articles.date_updated DESC
							LIMIT ".(($_GET['page']-1)*20).", 20");
	$arr = array();
	echo mysql_error();
	while ($result = mysql_fetch_object($query)) {
		$stack = "";
		if ($result->rank > 0.125) $stack = "right";
		else if ($result->rank < -0.125) $stack = "left";
		else $stack = "middle";
		$arr[] = array("id"=>$result->id,"stack"=>$stack,"html"=>"<b><a href=\"".$result->url."\">".$result->title."</a></b><br><p>".$result->description."</p>");
			
	}
	header("Content-Type:text/javascript");
	echo json_encode($arr);
	die;
}
