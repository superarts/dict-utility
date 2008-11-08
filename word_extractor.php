<?php

require("dic_extractor.php");
require("index_extractor.php");

function sd_is_valid($s)
{
	$ret = $s;
	$ret = str_replace("!", "", $ret);
	$ret = str_replace('?', '', $ret);
	$ret = str_replace('-', '', $ret);
	$ret = str_replace('*', '', $ret);
	$ret = str_replace('"', '', $ret);
	$ret = str_replace('`', '', $ret);
	$ret = str_replace('$', '', $ret);
	//	$ret = str_replace('/', '', $ret);
	//	$ret = str_replace('\\', '', $ret);

	if ($s == $ret)
		return true;
	else
		return false;
}

function sd_make_string($s)
{
	$ret = $s;
	$ret = str_replace("'", "''", $ret);

	return $ret;
}

function sd_get_meaning($word, $dict)
{
	/*
	exec("sdcv "$word" -u '$dict' > tmp");
	$r = file('tmp');
	 */
	//	$s = "sdcv \"$word\" -u '$dict'"; echo "$s\n";
	exec("sdcv \"$word\" -u '$dict'", $r);
	$s = '';
	for ($i = 4; $i < (count($r) - 1); $i++)
	{
		$s .= $r[$i] . "\n";
	}
	
	return $s;
}

function sd_should_insert($bookname)
{
	$s = "sqlite3 data/idict_info.db 'select * from info where bookname=\"$bookname\"'";
	$s = exec($s);
	if ($s == '')
		return true;
	else
		return false;
}

//	echo sd_make_string("one's will");

$fp_sql = fopen("output/idict_import.sh", "wb");

$dic_list = sd_get_filelist($sd_path);
//	print_r($dic_list);
//	for ($i = 0; $i < 1; $i++)
for ($i = 0; $i < count($dic_list); $i++)
{
	$filename_array = sd_get_filename($sd_path, $dic_list[$i], 'ifo');
	for ($index_filename = 0; $index_filename < count($filename_array); $index_filename++)
	{
		$filename = substr($filename_array[$index_filename], 0, -1);
			echo "filename: '$filename'\n";
		$bookname = sd_get_info($filename, 'bookname');
		$wordcount = sd_get_info($filename, 'wordcount');
		$author = sd_get_info($filename, 'author');
		if ($author == '')
			$author = 'StarDict';
		$dictname = str_replace('stardict', 'idict', $dic_list[$i]);
		$dictname = str_replace('2.4', '0', $dictname);
		$dictname = str_replace('-', '_', $dictname);
		echo "- $i -\n";
		echo "File: $dictname\n";
		echo "Name: $bookname\n";
		echo "Size: $wordcount\n";
		echo "Info: $author\n";

		//if ($dictname == 'idict_quick_eng_zh_CN_0.2')
		if (sd_should_insert($bookname))
		{
			$fp_txt = fopen("output/$dictname-$bookname.sql", "wb");
			//	$sql = "sqlite3 $dictname-$bookname.db 'create table dict(word text, meaning text);'\n";
			$sql = "create table dict(word text, meaning text);\n";
			fwrite($fp_txt, $sql);
			$sql = "begin transaction;\n";
			fwrite($fp_txt, $sql);
			//	echo "$sql\n";

			//	$filename = sd_get_filename($sd_path, $dic_list[$i], 'idx');
			$filename = substr($filename, 0, -3) . 'idx';
			//	echo "$filename\n";
			$index_list = sd_get_index($filename);
			//	print_r($index_list);
			
			//	for ($ii = 0; $ii < 10; $ii++)
			for ($ii = 0; $ii < count($index_list); $ii++)
			{
				if (($ii % 10) == 0) echo ".";
				if (($ii % 1000) == 0) echo "$ii\n";
				$index = $index_list[$ii];
				if (sd_is_valid($index) == true)
				{
					$meaning = sd_get_meaning($index, $bookname);
					$index = sd_make_string($index);
					$meaning = sd_make_string($meaning);
					$command = "insert into dict values('$index', '$meaning');\n";
					fwrite($fp_txt, $command);
					/*
					$sql = "sqlite3 $dictname.db \"insert into dict values('$index', '$meaning')\"";
					//	echo "($sql)\n";
					exec($sql);
					//	if ($error != '')
					//	echo "> $sql\n";
					 */
				}
			}
			//	$sql = "sqlite3 $dictname-$bookname.db .exit\n";
			//	fwrite($fp_txt, $sql);
			$sql = "commit;\n";
			fwrite($fp_txt, $sql);

			$sql = "sqlite3 -init '$dictname-$bookname.sql' '$dictname-$bookname.db' .exit\n";
			fwrite($fp_sql, $sql);
			//	echo "$sql\n";
			//	exec($sql);
			fclose($fp_txt);

			$sql = "sqlite3 data/idict_info.db 'insert into info values(\"$dictname\", \"$bookname\", \"$wordcount\", \"$author\", \"iDict - $bookname (Dictd)\")'";
			//	echo "$sql\n";
			exec($sql);
		}
		else	//	should insert bookname
		{
			echo "WARNING: duplicated dictionary $dictname\n";
		}
	}	//	for index_filename
}
fclose($fp_sql);

?>
