<?php //SITEMAP
require_once("site/config.php");
require_once("src/funktioner.php");
$schedule="monthly";
$today=$_SERVER['REQUEST_TIME'];
$list="";
$sql="SELECT P.name, P.edited FROM {$prefix}node AS P WHERE (P.type LIKE 'page') OR (P.type LIKE 'post')";
$log.="sql: $sql <br/>\n";
$res = $mysqli->query($sql); 
print_r($res);
if (is_object($res))
while ($i = $res->fetch_object()){
	$url=$base_url."/node/".$i->name;
	$edited = makeTimeStamp($i->edited);
$list.=<<<END
<url>
      <loc>$url</loc>
      <lastmod>$edited</lastmod>
      <changefreq>$schedule</changefreq>
      <priority>$priority</priority>
</url>
END;
}
else $log.="sql error!<br/>\n"; 

$sitemap=<<<END
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
$list
<url>
      <loc>$base_url/pages/posts</loc>
      <lastmod>$edited</lastmod>
      <changefreq>daily</changefreq>
      <priority>1</priority>
</url>
</urlset>
END;

file_put_contents( "sitemap.xml", $sitemap);

//-------------------------------

function makeTimeStamp ($dateTime) {
    if (!$dateTime) {
        $dateTime = date('Y-m-d H:i:s');
    }
    if (is_numeric(substr($dateTime, 11, 1))) {
        $isoTS = substr($dateTime, 0, 10);
    }
    else {
        $isoTS = substr($dateTime, 0, 10);
    }
    return $isoTS;
}
