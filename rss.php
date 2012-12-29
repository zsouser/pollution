<?php
// http://feeds.washingtonpost.com/rss/politics
class Article {
	public $title;
	public $desc;
	public $link;
	public $date;
	
	function __construct($item) {
		foreach ($item->children() as $child) {
			if ($child->getName() == "title")
				$this->title = $child;
			if ($child->getName() == "description")
				$this->desc = $child;
			if ($child->getName() == "link")
				$this->link = $child;
			if ($child->getName() == "pubDate")
				$this->date = $child;
		}
	}
	
	function __toString() {
		return "<div id='cloud'><a href='".$this->link."' id='title'>".$this->title."</a><p id='desc'>".$this->desc."</p>";
	}
	
	function save() {
		mysql_connect("localhost","zach","password");
		mysql_select_db("test");
		$query = mysql_query("select * from articles where url = '".mysql_real_escape_string($this->link)."'");
		$row = mysql_fetch_row($query);
		if (empty($row)) {
			echo "true";
			return mysql_query("insert into articles (title,url,description,date) values('".mysql_real_escape_string(strip_tags($this->title))."','".mysql_real_escape_string(strip_tags($this->link))."','".mysql_real_escape_string(strip_tags($this->desc))."','".mysql_real_escape_string(strip_tags($this->link))."')") && mysql_query("insert into rankings (article_id, rank) values ( last_insert_id(), 0 )");
		}
		else {
			echo "dupe";
			return false;
		}
	}
	
}
class Feed {
	public $articles;
	function __construct($link) {
		$xml = simplexml_load_file($link);
		foreach ($xml->children()->children() as $child) {
			if ($child->getName() == "item") {
				$this->articles[] = new Article($child);
			}
		}
	}
	
	function save() {
		foreach ($this->articles as $a) {
			if($a->save()) echo "good<br>";
			else echo mysql_error();
		}
	}
}

$feed = new Feed("http://rss.nytimes.com/services/xml/rss/nyt/Politics.xml");
$feed->save();

?>