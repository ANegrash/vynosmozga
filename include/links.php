<?
    $links = [];
    $res = mysql_query("
        SELECT 
            `category`, 
            `name`, 
            `link` 
        FROM 
            `links`
    ", $db);
    while ($row = mysql_fetch_array($res)) {
        $links[$row['category']][$row['name']] = $row['link'];
    }
?>