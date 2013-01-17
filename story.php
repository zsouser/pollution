<?php


require_once("sql.php");
require_once("facebook/facebook.php");
require_once("classes/cloud.class.php");

$facebook = new Facebook(array(
	'appId' => '487190331324891',
	'secret' => 'cc9d9a58fc57b6ec5e41835e3323b167'
));

$user = $facebook->getUser();


if (($_GET['page'] > 0)) {
	header("Content-Type:text/javascript");
	echo json_encode(Cloud::getPage($_GET['page']));
}

else if (isset($_GET['fbuid']) && $_GET['fbuid'] == $user) {
 	if ($_GET['stack'] == -1 || $_GET['stack'] == 0 || $_GET['stack'] == 1) {
		$stack = $_GET['stack'];
		$cloud = null;
		if ($_GET['do'] == 'vote' && $_GET['article'] > 0) {
			$article = mysql_real_escape_string($_GET['article']);
			$cloud = new Cloud($article); // this
		}
		else if ($_GET['do']== 'post' && is_url($_GET['url'])) {
			$url = mysql_real_escape_string($_GET['url']);
			$cloud = new Cloud($url);
		}
		if ($cloud !== null) {
			echo $cloud->vote($stack);
		}
	}
}