<?php
require_once("../site/config.php");
require_once("funktioner.php");
$mysqli = connectMySQL();

$sql=<<<FINITO
DROP TABLE IF EXISTS Projekt_node_tag;
DROP TABLE IF EXISTS Projekt_node;
CREATE TABLE Projekt_node (
id INT UNIQUE NOT NULL AUTO_INCREMENT, 
user_id INT NOT NULL DEFAULT 0,
name VARCHAR(15) UNIQUE NOT NULL,
title VARCHAR(40)  DEFAULT "unnamed node",  
type VARCHAR(15) DEFAULT "page",
content TEXT,

PRIMARY KEY (id),
KEY(name)
)  ENGINE = INNODB;

DROP TABLE IF EXISTS Projekt_user;
CREATE TABLE Projekt_user (
id INT NOT NULL UNIQUE, 
name VARCHAR(15) UNIQUE NOT NULL,
password VARCHAR(40) NOT NULL,  
type VARCHAR(15) DEFAULT "user",
content TEXT,

PRIMARY KEY (id)
)  ENGINE = INNODB;
INSERT INTO Projekt_user (id, name, password, type, content) VALUES (1, "Roland", MD5("pjomss5d"), "admin", "this is the default account");

DROP TABLE IF EXISTS Projekt_node_tag;
CREATE TABLE Projekt_node_tag (
    node_id INT NOT NULL, 
    tag_id INT NOT NULL, 
    PRIMARY KEY (node_id, tag_id),
	CONSTRAINT Projekt_node_id
	FOREIGN KEY (node_id) REFERENCES Projekt_node(id),
	CONSTRAINT Projekt_tag_id
	FOREIGN KEY (tag_id) REFERENCES Projekt_node(id)
)  ENGINE = INNODB;

FINITO;

$sql2=<<<FINITO
DROP PROCEDURE IF EXISTS edit_node; 
CREATE PROCEDURE edit_node(
IN in_node_id INT, 
IN in_name VARCHAR(30), 
IN in_user_id INT, 
IN in_title VARCHAR(30), 
IN in_type VARCHAR(30), 
IN in_content TEXT
)
BEGIN
IF (SELECT COUNT(*) FROM Projekt_node WHERE id = in_node_id OR name LIKE in_name)<1
THEN
INSERT INTO Projekt_node (type, name, user_id, title, content) VALUES (in_type, in_name, in_user_id, in_title, in_content);
SELECT LAST_INSERT_ID();
ELSE
UPDATE Projekt_node SET content = in_content, title=in_title, name=in_name WHERE id = in_node_id OR name LIKE in_name;
SELECT in_node_id;
END IF;
END;

INSERT INTO Projekt_node (type, name, user_id, title, content) VALUES ('page', 'main', 0, 'main page', 'lorem ipsum');
INSERT INTO Projekt_node (type, name, user_id, title, content) VALUES ('page', 'info', 1, 'info page', 'lorem ipsum 2');

DROP PROCEDURE IF EXISTS tag_node; 
CREATE PROCEDURE tag_node(
IN in_node_id INT, 
IN in_tag VARCHAR(30), 
IN in_content TEXT
)
BEGIN
IF (SELECT COUNT(*) FROM Projekt_node WHERE name LIKE in_tag)<1
THEN
INSERT INTO Projekt_node (name, user_id, title, content) VALUES (in_name, in_user_id, in_title, in_content);
SELECT LAST_INSERT_ID() INTO @tag_id;
ELSE
SELECT id FROM Projekt_node WHERE name LIKE in_tag LIMIT 1 INTO @tag_id;
END IF;
IF (SELECT COUNT(*) FROM Projekt_node_tag WHERE (node_id = in_node_id) AND (tag_id = @tag_id) )<1
THEN
INSERT INTO Projekt_node_tag (node_id, tag_id, name, content) VALUES (in_node_id, @tag_id, in_tag, in_content);
END IF;
END;
SELECT "Successfully constructed database!";
FINITO;

call_SQL($sql);
call_SQL($sql2);


//-----------------
function call_SQL($sql){
 global $mysqli;
  	$dbLink=$mysqli;
	$res = $dbLink->multi_query($sql) or die("Could not query database, query: <br/><pre>{$sql}</pre>error: <br/><pre>{$dbLink->error}</pre><br/>");
        if($dbLink->more_results())
            {
                $result = $dbLink->use_result();
                $output = array();
                if (is_object($result)){
                while($row = $result->fetch_assoc())
                  $output[] = $row;                 
                $result->free();}
                while($dbLink->more_results() && $dbLink->next_result())
                {
                    $extraResult = $dbLink->use_result();
                    if($extraResult instanceof mysqli_result){
                        $extraResult->free();
                    }
                }
                if ($mysqli->errno) { 
    echo "Batch execution prematurely ended on statement $i.\n"; 
    var_dump($statements[$i], $mysqli->error); 
} 
                echo("MySQL Result: ".print_r($output, true));
                return $output;
}
}
