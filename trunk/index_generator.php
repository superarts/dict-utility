<?php

exec("ls ./data/db/*db", $r);
print_r($r);
foreach ($r as $s)
{
	system("sqlite3 $s 'create index index_word on dict(word);'");
}

?>
