<?
    $hostname = "...";
    $username = "..."; 
    $password = "..."; 
    $dbName = "vynosmozga";

    $db = mysql_connect($hostname, $username, $password); 
    mysql_select_db($dbName);
    mysql_query("SET NAMES 'UTF8'");
?>