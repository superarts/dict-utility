<?php

$filename = "/home/leo/doc/dictionary/quick/stardict-quick_wel-swe-2.4.2/quick_walesiska-svenska.idx";
//	$filename = "/usr/share/stardict/dic/stardict-langdao-ec-gb-2.4.2/langdao-ec-gb.idx";

function sd_read_string($fp)
{
	$s = "";
	$i = false;

	while ($i === false)
	{
		$s .= fread($fp, 100);
		$i = strpos($s, "\0");
	}
	fseek($fp, $i - strlen($s) + 9, SEEK_CUR);
	//	echo ftell($fp);	echo "\n";

	return substr($s, 0, $i);
}

function sd_get_index($filename)
{
	$ret = array();
	$count = 0;

	$filesize = filesize($filename);
	$fp = fopen($filename, "rb");
	while (ftell($fp) < $filesize)
	{
		$s = sd_read_string($fp);
		$count++;
		//	echo "found $count:\t$s\n";
		$ret[] = $s;
	}
	fclose($fp);

	return $ret;
}

/*
$r = sd_get_index($filename);
print_r($r);
 */

?>
