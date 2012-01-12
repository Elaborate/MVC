<?php

class Node{
	public $prefix="";
	public $title="unnamed"; 
	public $content="This page has no content. <a href='/edit'>Click to Edit</a>";
	public $type="error";
	public $id='main';
	public $idUser=0;
	public $name=false;
	public $tags;
	public static $log="Log for Node class";
	public static $nodes;	
	public static $mysqli;
	private function log($text=""){  Node::$log.="<br/>\n$text";}
	public function getLog(){ return "<br/>\n" . Node::$log;}
	private function shutdown($text=""){die("$text" . $this->getLog() );}



	//------------------------------
public function __construct($id='main', $load=1, $obj=false){
	global $mysqli, $prefix;
	$this->log("__construct($id, {$load})");
	Node::$mysqli = $this->mysqli = $mysqli;
	$this->prefix=$prefix;
	$this->base_url=$_SESSION['base_url'];
	$this->tags = array();
	if (isset($_SESSION['idUser'])) $this->idUser=$_SESSION['idUser'];
	$row=false;
	
	if ($obj) $this->fillNode($obj);
	else if (($load)||(!is_numeric($id))) $this->loadNode($id);
	else $this->id = $this->node = $id;
	Node::$nodes[]=$this;
	} 	
//----------------------------------

public static function load($sql){
	global $mysqli, $prefix;
	Node::$mysqli = $mysqli;
	//$res = Node::call_sql($sql);
	$res = $mysqli->query($sql); 
	print_r($res);
	while ($row=$res->fetch_object())
	//foreach($res AS $row)
		$node = new Node(0,false, $row);
		//node is automatically added to static array.
		return Node::$nodes;
	}
	//--------------------------------

	
	//--------------------------------
private function loadNode($id=false){
	$this->log("loadNode($id)");
	//	  if (!$id) $this->content=""; 
	  if (is_numeric($id)) $row = $this->getNode($id);
	  else if (is_string($id)){
	  	  $this->name = "".$id;
	  	  $row = $this->getNodeByName($id);	  
	  }
	  $this->fillNode($row, true);
	}
	//--------------------------------
	
private function fillNode($row, $tags=false){
	Node::log("fillNode(object, $tags)");
	if (isset($row->title)){
		Node::log("title: ".$row->title);
		$this->title = $row->title;
		$this->content = $row->content;
		$this->type = $row->type;
		$this->id = $row->id;
		$this->node = $row->id;
		$this->name = $row->name;
		if ($tags) $this->tags = $this->getTags($this->id);		
	 	}
	 else $this->content="This page has no content. <a href='{$_SESSION['base_url']}/edit/$id'>Click to Edit</a>";
	}
	
	//--------------------------------
private function getQuery($table, $id, $active=""){
	$this->log("getQuery($table, $id, $active)");
	$pre=$this->prefix;
	if ($active) $active = "AND active = true";
	$query = "SELECT * FROM $pre$table WHERE node = $id $active ORDER BY node ASC;";
	$res = $this->mysqli->query($query) 
	or $this->shutdown("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html="";
	$row = $res->fetch_object();
	$res->close();
	return $row; 
	}

	//-------------------------------
public function call_SQL($sql){
  	Node::log("call_SQL($sql)");
  	$dbLink=Node::$mysqli;
	$res = $dbLink->multi_query($sql) or die("Could not query database, query: <br/><pre>{$sql}</pre>error: <br/><pre>{$dbLink->error}</pre><br/>");
        if($dbLink->more_results())
            {
                $result = $dbLink->use_result();
                $output = array();
                while($row = $result->fetch_assoc())
                  $output[] = $row;                 
                $result->free();
                while($dbLink->more_results() && $dbLink->next_result())
                {
                    $extraResult = $dbLink->use_result();
                    if($extraResult instanceof mysqli_result){
                        $extraResult->free();
                    }
                }
                Node::log("MySQL Result: ".print_r($output, true));
                return $output;
}
else Node::log("No Result... ".$dbLink->error);
  }	
  
	//--------------------------
private function getNode($id){
	$this->log("getNode($id)");
	$pre=$this->prefix;
	$query = "SELECT * FROM {$pre}node WHERE id = $id;";
	$res = $this->mysqli->query($query) 
	or $this->shutdown("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html="";
	$row = $res->fetch_object();
	$res->close();
	$this->log("result:".print_r($row, true));
	
	
	return $row; 
	}

	
private function getNodeByName($name){
	$this->log("getNodeByName('$name')");
	$pre=$this->prefix;
	$query = "SELECT * FROM {$pre}node WHERE name LIKE '$name';";
	$this->log("query: $query");
	$res = $this->mysqli->query($query) 
	or $this->shutdown("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html="";
	$row = $res->fetch_object();
	$res->close();
	$this->log("result:" . print_r($row, true));
	return $row; 
	}	
//--------------------------------


public function getTags(){
	$id = $this->node;
	$pre=$this->prefix;
	$arr = array();
	$this->log("getTags($id)");
		$query = "SELECT T.* FROM {$pre}node AS T, {$pre}node_tag AS NT WHERE T.type LIKE 'tag' 
		AND NT.tag_id = T.id 
		AND NT.node_id = $id;";
	$res = $this->mysqli->query($query) 
	or $this->shutdown("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html="";
	while ($row = $res->fetch_object())
		$arr[$row->id] = $row->name;
	$res->close();
	$this->tags_text = implode(', ', $arr);
	$this->log("tags: ".$this->tags_text." ".print_r($arr, true));
	return $row;
	}

//---------------------------------
public function editNode(){
	$this->log("editNode($id)");
	$content=$this->content;
	$node=$this->node;
	$name=$this->name;
	$author=$this->idUser;
	$title=$this->title;
	$tags = implode(', ', $this->tags);
	$base_url = $this->base_url;
	$name = $_SESSION['current_page'];

return<<<END
	<fieldset>
	  <legend>Edit Post</legend>
	  <form action='{$base_url}/Save/$name' 
	  	method='get' 
	  	class="edit_page">
	Title: <input type="text" name="title" value="$title"/><br/>
	Name: <input type="text" name="name" value="$name"/><br/>
	Tags: <input type="text" name="tags" value="$tags"/><br/>
	<input type="radio" name="type" value="post"/> Post <br/>
	<input type="radio" name="type" value="comment"/> Comment <br/>
	<input type="radio" name="type" value="page"/> Page <br/>
	Blueprint.css: <input type="checkbox" name="blueprint" /><br/>
	Javascript <input type="checkbox" name="javascript" /><br/>
	Content: <textarea name="content">$content</textarea>
	<button type="submit" 
		name="action" 
		value="post">
		Save Page</button>
	  </form>
	</fieldset>
END;
}

//--------------------------
private function request($x, $alt="", $alt2=false){
	if (isset($_REQUEST[$x]))
		return $_REQUEST[$x];
		else if ($alt) return $alt;
		else return $alt2;
}
//--------------------------

public function update(){
	$this->log("update()");
	$node=$this->node;
	$author=$this->idUser;
	$base_url = $this->base_url;
	$node = $this->node;
	
	$this->log("REQUEST: ".print_r($_REQUEST, true));
	$content=$this->request('content', $this->content, "");
	$title =$this->request('title', $this->title, "untitled");
	$tags = $this->request('tags', $this->tags, "");
	$name = $this->request('name', $this->name);
	$type = $this->request('type', $this->type, 'post');	
	
	$this->uploadNode($node, $name, $author, $title, $content, $tags, $type);
}

//---------------------------------------
public function uploadNode($id=false, $name=false, $author=false, $title="", $content="", $tags="", $type="page"){
	$this->log("uploadNode($id, '$name', $author, '$title', '$content', '$tags', '$type')");
	$this->log("everything checks out!");
	if (!$id) $id = -1; //$this->shutdown("no node id");
$this->log("everything checks out! 2");	
	if (!$author) $this->shutdown("no user id");
	$this->log("everything checks out! 3");	
	if (!is_numeric($id)) $this->shutdown("node id not numeric");
	$this->log("everything checks out! 4");	
	if (!$name) $name = $id;
$this->log("everything checks out! 5");	
$sql="CALL edit_node($id, '$name', $author, '$title', '$type', '$content'); \n";

if ($tags){
	$tags = explode(',', $tags);
	foreach ($tags as $i => $j) 
		$sql.="CALL tag_node($id, $author, '{$j}','this is a tag'); \n";
}
$this->log("calling with sql=$sql");
$this->call_SQL($sql);
$this->log("called");
}


//---------------------------------------
	
public function getComments(){
	$node=$this->node;
	$prefix = $this->prefix;
	$query = "SELECT N.* FROM {$prefix}node AS N, {$prefix}node_node AS NN   
		  WHERE N.type LIKE 'comment'
		  AND NN.parent_id = $node
		  AND NN.child_id = N.id
		  ORDER BY node ASC;";
	$res = $this->mysqli->query($query) 
	or $this->shutdown("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$html=array();
	while ($row = $res->fetch_object())
		$nodes[]=$row;
	$res->close();
	return $nodes;
}

public function getNumberOfComments(){
	$node=$this->node;
	$prefix = $this->prefix;
	$query = "SELECT COUNT(*) AS number FROM {$prefix}node_node GROUP BY child_id HAVING parent_id = $node;";
	$res = $this->mysqli->query($query) 
	or $this->shutdown("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$row = $res->fetch_object();
		$nr.=$row->number;
	$res->close();
	return $nr;
}

public static function getPostsFromTag($tag){
	$prefix = $GLOBALS['prefix'];
	$mysqli = $GLOBALS['mysqli'];
	$query = "SELECT DISTINCT Node.id, Node.* 
		  FROM {$prefix}node_tag AS NT, 
		  {$prefix}node AS Tag, 
		  {$prefix}node AS Node
		  WHERE N.name LIKE '%$tag%'
		  AND NN.tag_id = Tag.id
		  AND NN.node_id = Node.id
		  ORDER BY node ASC;";
	$res = $mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$nodes=array();
	while ($row = $res->fetch_object())
		$nodes[]=$row;
	$res->close();
	return $nodes;
	
}

public function getPostsFromTime($daysAgo){
	$prefix = $GLOBALS['prefix'];
	$mysqli = $GLOBALS['mysqli'];
	$query = "SELECT * {$prefix}node AS Node WHERE  
	(edited >= CURRENT_DATE - INTERVAL $daysAgo DAY AND 
	edited < CURRENT_DATE - INTERVAL 0 DAY)";
	$res = $mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$nodes=array();
	while ($row = $res->fetch_object())
		$nodes[]=$row;
	$res->close();
	return $nodes;
}

public function getPostsFromUser($userID){
	$prefix = $GLOBALS['prefix'];
	$mysqli = $GLOBALS['mysqli'];
	$query = "SELECT * FROM {$prefix}node WHERE user_id=$userID";
	$res = $mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$nodes=array();
	while ($row = $res->fetch_object())
		$nodes[]=$row;
	$res->close();
	return $nodes;
}

public function getPostNumberFromTime($daysAgo){
	$prefix = $GLOBALS['prefix'];
	$mysqli = $GLOBALS['mysqli'];
	$query = "SELECT COUNT(*) AS number FROM {$prefix}node WHERE 
	(edited >= CURRENT_DATE - INTERVAL $daysAgo DAY AND 
	edited < CURRENT_DATE - INTERVAL 0 DAY)";
	$res = $mysqli->query($query) 
	or die("Could not query database with query '$query'  "
		. print_r($res). $this->mysqli->error);
	$days=0;
	$row = $res->fetch_object();
	$nr = $res->number;
	$res->close();
	return $nr;
}  


}
