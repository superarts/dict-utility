<?php

$sd_sql_path = "./output";
exec("ls $sd_sql_path/*.sql", $r);
foreach ($r as $s)
{
	//	$sql = "sqlite3 -init $dictname-$bookname.sql $dictname-$bookname.db .exit\n";
	$filename = substr($s, 0, -3) . "db";
	$sql = "sqlite3 -init $s $filename .exit\n";
	echo $sql;
}

?>
