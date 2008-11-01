<?php

$sd_path = exec('echo $STARDICT_DATA_DIR');

function sd_get_filelist($path)
{
	$ret = array();

	exec("ls $path > tmp");
	$sd_list = file("tmp");

	for ($i = 0; $i < count($sd_list); $i++)
	{
		$s = $sd_list[$i];
		$s = substr($s, 0, -1);
		$ret[] = $s;
		//	echo "'$s'\n";
	}
	//	echo "sd path: $path\n";
	//	print_r($sd_list);
	
	return $ret;
}

function sd_get_filename($path, $file, $ext)
{
	system("ls $path/$file/*$ext > tmp");
	return file('tmp');		//"$path/$file/$s";
}

/*
 * key list:
 * 		wordcount
 * 		bookname
 * 		author
 *
 * 		version
 * 		idxfilesize
 * 		description
 * 		date
 */
function sd_get_info($filename, $key)
{
	$s = exec("cat $filename | grep $key");
	//	echo "get info: 'cat $filename | grep $key'\n";
	return substr($s, strlen($key) + 1);
}
function sd_get_dictionary()
{
	$ret = array();

	system("sdcv -l > tmp");
	/*
	$fp = fopen("tmp", "rb");
	fclose($fp);
	 */
	$a = file("tmp");
	//	print_r($a);

	for ($i = 1; $i < count($a); $i++)
	{
		$s = $a[$i];
		$tail = strpos($s, '    ');
		$s = substr($s, 0, $tail);
		//	echo "'$s'\n";
		$ret[] = $s;
	}

	return $ret;
}

/*
$r = sd_get_filelist($sd_path);
$s = sd_get_filename($sd_path, $r[41], "ifo");
echo "$s\n";
$s = sd_get_info($s, "bookname");
echo "$s\n";
//	$r = sd_get_dictionary();
//	print_r($r);
 */

?>
