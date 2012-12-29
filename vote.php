<?php
header("Content-Type: text/javascript");
if (isset($_GET['article']) && isset($_GET['stack'])) {
	mysql_connect("localhost","zach","password"); // set for password
	mysql_select_db("test");
	$query = mysql_query("INSERT INTO rankings (article_id,rank) VALUES ('".mysql_real_escape_string($_GET['article'])."','".mysql_real_escape_string($_GET['stack'])."')");
	if ($query) {
		$query1 = mysql_query("update articles set date_updated = NULL where id = '".$_GET['article']."'");
		echo mysql_error();
		$query2 = mysql_query("select avg(rank) as r from rankings where article_id = '".$_GET['article']."' group by article_id");
		$result = mysql_fetch_object($query2);
		if ($result->r > 0.125)  echo "right";
		else if ($result->r < -0.125) echo "left";
		else echo "middle";
	}
	else echo mysql_error();
}
?>