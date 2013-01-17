<?php



class Cloud {
	public $user;
	public $id;
	public $rank;
	public $title;
	public $description;
	public $url;
	const DUPE = 0;
	const INVALID_URL = 1;
	const NO_DATA = 2;
	
	public static function getPage($page) {
		$q = "
			SELECT articles.*, avg(rankings.rank) as rank
			FROM articles
			LEFT JOIN rankings ON articles.id = rankings.article_id 
			GROUP BY rankings.article_id
			ORDER BY articles.date_updated DESC
			LIMIT ".(($page-1)*20).", 20";
		$query = mysql_query($q);
		$arr = array();
		while ($result = mysql_fetch_object($query)) {
			$cloud = new Cloud($result);
			if ($cloud != null) {
				$arr[] = $cloud->toArray();
			}
		}
		return $arr;
	}
	
	public function __construct($result) {
		global $user;
		if (self::is_url($result)) {
			$res = $this->submit($result);
			if (is_object($res)) return $res;
			else {
				switch ($res) {
					case self::DUPE:
						echo "dupe";
						break;
					case self::INVALID_URL:
						echo "bad";
						break;
					case self::NO_DATA:
						echo "no data";
						break;
				}
				return null;
			}
		} else if (!is_object($result) && $result > 0) {
			$q = "
				SELECT articles.*, avg(rankings.rank) as rank
				FROM articles
				LEFT JOIN rankings ON articles.id = rankings.article_id
				GROUP BY rankings.article_id
				WHERE articles.id = '$result'
				";
			$query = mysql_query($q);
			$res = mysql_fetch_object($query);
			return new Cloud($res);
		} else {
			if ($result->id == null) return null;
			$this->user = $user;
			$this->id = $result->id;
			$this->rank = $result->rank;
			$this->title = $result->title;
			$this->description = $result->description;
			$this->url = $result->url;
		}
	}
	
	private function submit($url) {
		if (self::is_url($url)) {
			$metas = getMetas(new DOMDocument(),$url);
			$title = mysql_real_escape_string($metas["og:title"]);
			$link = mysql_real_escape_string($metas["og:url"]);
			$desc = mysql_real_escape_string($metas["og:description"]);
			
			if (empty($title) || empty($link)) return self::NO_DATA;
		
			$query = mysql_query("select * from articles where url = '$link'");
			$row = mysql_fetch_row($query);
		
			if (empty($row)) {
				$q1 = mysql_query("insert into articles (title,url,description) values('$title','$link','$desc')");
				$id = mysql_insert_id();
				if ($q1 && $q2) {
					return new Cloud($id);			
				} 
			}
			return self::DUPE;
		}
		return self::INVALID_URL;
	}
	
	private function getMetas($dom,$url) {
		$dom->loadHTMLFile($url);
		$xpath = new DOMXPath($dom);
		$query = '//*/meta[starts-with(@property, \'og:\')]';
		$metas = $xpath->query($query);
		$rmetas = array();
		foreach ($metas as $meta) {
			$property = $meta->getAttribute('property');
			$content = $meta->getAttribute('content');
			$rmetas[$property] = $content;
		}
		return $rmetas;
	}
	
	public function vote($stack) {
		if ($this->hasVoted()) {
			return "dupe";
		}
		$query = mysql_query("INSERT INTO rankings (article_id,rank,fbuid) VALUES ('".$this->id."','$stack','".$this->user."')");
		if ($query) {
			$query1 = mysql_query("update articles set date_updated = NULL where id = '".$this->id."'");
			if (!$query1) return "error1";
			$query2 = mysql_query("select avg(rank) as r from rankings where article_id = '".$this->id."' group by article_id");
			$result = mysql_fetch_object($query2);
			$this->rank = $result->r;
			return $this->getStack();
		}	 
		return "error";
	}
	
	public function getStack() {
		if ($this->rank > 0.125) return "right";
		if ($this->rank < -0.125) return "left";
		if ($this->rank < 0.125 && $this->rank > -0.125) return "middle";
		return "error";
	}
	
	private function hasVoted() {
		$query = mysql_query("SELECT * FROM rankings WHERE article_id = '".$this->id."' AND fbuid = '".$this->user."'");
		if ($query) {
			$result = mysql_fetch_row($query);
			return !empty($result);
		}
		return true;
	}

	public function __toString() {
		
		$html = "
			<b><a target='_blank' href=\"".$this->url."\">".$this->title."</a></b><br>
			<p>".htmlentities(strip_tags($this->description))."</p>";
		
		if (!$this->hasVoted())
			$html .= "<div id='vote' data-article='".$this->id."'>
				<a class='vote' data-stack='-1' id='left-vote'>&larr;</a>
				<a class='vote' data-stack='0' id='middle-vote'>&uarr;</a>
				<a class='vote' data-stack='1' id='right-vote'>&rarr;</a>
			</div>
			";
	  		
		return $html;
	}
	
	public function toArray() {
		return array("id"=>$this->id,"stack"=>$this->getStack(),"html"=>$this->__toString());
	}
	
	public static function is_url($url) {
		if (is_string($url))
		return preg_match("/^((https?|ftp)\:\/\/)?([a-z0-9-.]*)\.([a-z]{2,3})(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?$/",$url);
		return false;
	}
}


?>