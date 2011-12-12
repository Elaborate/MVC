<?php
class CContent {
	public $prefix;
	public $title="unnamed"; 
	public $content="This page has no content. <a href='/edit'>Click to Edit</a>";
	public $type="error";
	public $id=0;

	
public function __construct($mysqli, $prefix="", $id=false){
	$this->mysqli=$mysqli;
	$this->prefix=$prefix;
	$row=false;
	
	if (!$id) $this->content=""; 
	else if (is_numeric($id)) $row = $this->getNode($id);
	else if (is_string($id)) $row = $this->getNodeByName($id);

	if (isset($row->title)){
		$this->title = $row->title;
		$this->content = $row->content;
		$this->type = $row->type;
		$this->id = $row->id;
	 	}
	 else $this->content="This page has no content. <a href='{$_SESSION['base_url']}/edit/$id'>Click to Edit</a>";
	 
	} 	
	
private function getQuery($table, $id, $active=""){
	$pre=$this->prefix;
	if ($active) $active = "AND active = true";
	$query = "SELECT * FROM $pre$table WHERE node = $id $active ORDER BY node ASC;";
	$res = $this->mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html="";
	$row = $res->fetch_object();
	$res->close();
	return $row; 
	}

private function getNode($id){
	$pre=$this->prefix;
	$query = "SELECT * FROM {$pre}node WHERE node = $id;";
	$res = $this->mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html="";
	$row = $res->fetch_object();
	$res->close();
	return $row; 
	}
	
private function getNodeByName($id){
	$pre=$this->prefix;
	$query = "SELECT * FROM {$pre}node WHERE name LIKE '$id';";
	$res = $this->mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html="";
	$row = $res->fetch_object();
	$res->close();
	return $row; 
	}	
	
	public function getAvatar2($user){
$query = "SELECT url FROM blog_avatar WHERE idUser=$user and active=true;";
$res = $this->mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html="";
	$row = $res->fetch_object();
	$res->close();
return $row->url;
}
	
public function getComments($node=false){
	
	$query = "SELECT * FROM blog_comment WHERE idPost = $node ORDER BY node ASC;";
	$res = $this->mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html="";
	while ($row = $res->fetch_object())
		$html.=$this->comment($row);
	$res->close();
	return $html;
}

public function getNumberOfComments($node=false){
	if (!$node) return false;
	$query = "SELECT COUNT(*) AS number FROM blog_comment GROUP BY idPost HAVING idPost = $node;";
	$res = $this->mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html="";
	$row = $res->fetch_object();
		$nr.=$row->number;
	$res->close();
	return $nr;
}

public function getPostsFromTag($node){
	$tag = getValue('blog_tag',$node, 'text');
	$query = "SELECT * FROM blog_post WHERE tags LIKE '%$tag%' ORDER BY node ASC;";
	$res = $this->mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html="";
	while ($row = $res->fetch_object())
		$html.=$this->post($row);
	$res->close();
	return $html;
}

public function getPostsFromTime($daysAgo){
	$query = "SELECT * FROM blog_post WHERE 
	(date >= CURRENT_DATE - INTERVAL $daysAgo DAY AND 
	date < CURRENT_DATE - INTERVAL 0 DAY)";
	$res = $this->mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html="";
	while ($row = $res->fetch_object())
		$html.=$this->post($row);
	$res->close();
	return $html;
}

public function getPostsFromUser($userID){
	$query = "SELECT * FROM blog_post WHERE 
	idUser=$userID";
	$res = $this->mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html="";
	while ($row = $res->fetch_object())
		$html.=$this->post($row);
	$res->close();
	return $html;
}

public function getPostNumberFromTime($daysAgo){
	/*
	$query = "SELECT COUNT(*) as count FROM blog_post GROUP BY date HAVING (date >= CURRENT_DATE - INTERVAL $daysAgo DAY)";
	$res = $this->mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html="";
	$row = $res->fetch_object();
	$res->close();
	return $row->count;
	*/
	$query = "SELECT * FROM blog_post WHERE 
	(date >= CURRENT_DATE - INTERVAL $daysAgo DAY AND 
	date < CURRENT_DATE - INTERVAL 0 DAY)";
	$res = $this->mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$days=0;
	while ($row = $res->fetch_object())
	$days++;
	$res->close();
	return $days;
}

public function getPost($node){
	$row=$this->getQuery('blog_post', $node);
	$html= $this->post($row);
	//$html.= $this->commentForm($node);
	return $html;
}

public function getEditPost($node){
	$row=$this->getQuery('blog_post', $node);
	return $this->editPost($row);
}

public function getUser($node){
	$row=$this->getQuery('blog_user', $node);
	return $this->user($row);
}

public function editUserForm($node){
	$row=$this->getQuery('blog_user', $node);
	return $this->editUser($row);
}

public function getComment($node){
	$row=$this->getQuery('blog_comment', $node);
	return $this->comment($row);
}

public function getSignature($node){
	$row=$this->getQuery('blog_signature', $node, true);
	$html = $this->post($row);
	//$html.= $this->commentForm($node);
	return $html;
	
}

public function getAvatar($node){
	$row=$this->getQuery('blog_avatar', $node, true);
	return $this->post($row);
}

public function post($row){	
	$text=$row->text;
	if (!$text) return "";
	$node=$row->node;
	$author=$row->idUser;
	$title=$row->title;
	//$name=$row->name;
	$date=$row->date;
	$tags=$row->tags;
	$name=getValue('blog_user',$author, 'name');
	$nr=$this->getNumberOfComments($node);
	if ($nr>0) $nr = "<a href='?p=$node'>$nr comments</a>";
	$html=<<<END
	<article>
	<header>
	<h1><a href='?p=$node'>$title</a></h1>
	<h2>by: <a href='?p=$author'>$name</a></h2>
	</header>
	<p>$text</p>
	<footer>
	$nr
	Tags: $tags
	<br/><span>Posted: $date</span>
	</footer>
	</article>
END;
return $html;
}

public function postRSS($row){
	$text=$row->text;
	if (!$text) return "";
	$node=$row->node;
	$author=$row->idUser;
	$title=$row->title;
	//$name=$row->name;
	$date=$row->date;
	$tags=$row->tags;
	$name=getValue('blog_user',$author, 'name');
	$nr=$this->getNumberOfComments($node);
	if ($nr>0) $nr = "<a href='?p=$node'>$nr comments</a>";
	
	$html=<<<END
        <item>
                <title>$title</title>
                <description>$text</description>
                <link>{$_SERVER['DOCUMENT_ROOT']}?/p=$node</link>
                <guid>unique string per item</guid>
                <pubDate>$date</pubDate>
        </item>
END;
return $html;
}



public function getPostsRSS(){
	$query = "SELECT * FROM blog_post ORDER BY node ASC;";
	$res = $this->mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $mysqli->error);

//DISPLAY
while($row = $res->fetch_object()) {
	$html .= $this->postRSS($row);
}
$res->close();

return<<<END
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
        <title>Two Thirds Done</title>
        <description>The blog posts of a procrastinator</description>
        <link>{$_SERVER['DOCUMENT_ROOT']}</link>
        <lastBuildDate>Mon, 06 Sep 2010 00:01:00 +0000 </lastBuildDate>
        <pubDate>Mon, 06 Sep 2009 16:45:00 +0000 </pubDate>
 $html
</channel>
</rss>
END;
}

public function separateTags($tags){
	$pattern="/[|.,\s]*?/";
  $string = preg_replace($pattern, ' ', $tags);
  return explode(' ', $tags);
  }

public function enterTags($tags){
  $array = $this->separateTags($tags);
  foreach ($array as $i=>$j){
  	  echo "tag: $j<br/>";
  	  if (!$this->tagExists($j)) $this->insertTag($j);
  }
}

  public function tagExists($tag){
	$id=createID('blog_tag');
	$query="SELECT node FROM blog_tag WHERE text LIKE '$tag';";
	$res = $this->mysqli->query($query);
	if($res->num_rows === 1) return true;
	else return false;
  }
  
public function insertTag($tag){
	echo "inserting tag";
	$id=createID('blog_tag');
	$query="INSERT INTO blog_tag(node, text) VALUES ($id,'$tag');";
	$res = $this->mysqli->query($query); 
  }
  

public function user($row){
		$name=$row->name;
	if (!$name) return "";
	
	$html=<<<END
	<h1>$name</h1>
END;
return $html;
}

public function comment($row){
	$text=$row->text;
	if (!$text) return "";
	$idPost=$row->node;
	$author=$row->idUser;
	$email=$row->email;
	$title=$row->title;
	$pos=strpos($email, '@');
	$name=substr($email, 0, $pos);
	$signature=$this->getSignature($idPost);
	$html=<<<END
	<div class="comment">
	  <header>
	  <a href='?p=$node'><h2>$title</h2>
	  <h3>by: $name</h3></a>
	  <button type=submit name=grade value=1>+</button>
	  <button type=submit name=grade value=0>-</button>
	  </header>
	  <p>$text</p>
	</div>
	  <footer>
	$signature
	<a class="reply" href="?action=comment&amp;node=$idPost"> 
	
	</footer>
END;
return $html;
}

public function signature($row){
	$text=$row->text;
	$html=<<<END
	$text 
END;
return $html;
}

public function postForm($id=false){
  $html="";
  if (loggedIn()=='usr')
  $html.=<<<END
	<fieldset>
	  <legend>Add Post</legend>
	  <form action='{$_SERVER['PHP_SELF']}' method='get'>
	<p>Title: <input type="text" name="title" value=""/></p>
	<p>Post: <textarea name="blog_text"></textarea></p>
	<p>Tags: <input type="text" name="tags" value=""/></p>
	<button type="submit" 
		name="action" 
		value="post">
		Add Post</button>
	  </form>
	</fieldset>
END;
	return $html;
}

public function editPost($row){
	$text=$row->text;
	$idPost=$row->node;
	$author=$row->idUser;
	$title=$row->title;
	$name=$row->name;

$html=<<<END
	<fieldset>
	  <legend>Edit Post</legend>
	  <form action='{$_SERVER['PHP_SELF']}' method='get'>
	Title: <input type="text" name="title" value="$title"/>
	Post: <textarea name="blog_text">$text</textarea>
	Tags: <input type="text" name="tags" value="$tags"/>
	<button type="submit" 
		name="action" 
		value="post">
		Add Post</button>
	  </form>
	</fieldset>
END;
}

public function editUser($row){
	$name=$row->name;
	$idPost=$row->email;
	$idUser=$row->idUser;
	$title=$row->title;
	$name=$row->name;

$html=<<<END
	<fieldset>
	  <legend>Edit Post</legend>
	  <form action='{$_SERVER['PHP_SELF']}' method='get'>
	Name: <input type="text" name="title" value="$name"/>
	Email: <input type="text" name="tags" value="$email"/>
	<button type="submit" 
		name="action" 
		value="editUser">
		Add Post</button>
	  </form>
	</fieldset>
END;

return $html;
}

public function commentForm($id){
  $html="";
  $html.=<<<END
	<fieldset>
	  <legend>Add Comment</legend>
	  <form action='{$_SERVER['PHP_SELF']}' method='get'>
	      <input type=hidden name=node value='$id' />
	<p>Title: <input type="text" name="title" value=""/></p>
	<p>Email: <input type="text" name="email" value=""/></p>
	<p>Text: <textarea name="text"/></textarea></p>
	<button type="submit" 
		name="action" 
		value="comment">
		Add Comment</button>
	  </form>
	</fieldset>
END;
	return $html;
}


}
