<?php

$quick_series = false;
$quick_deluxe = false;
$freedict_series = true;
$freedict_deluxe = true;

require("sd_path.php");

if ($quick_series)
{
	$sd_name_prefix = "iDict";
	$sd_name_postfix = "Quick";
	$sd_term = array(
		'afr',
		'bul',
		'zh_CN',
		'czech',
		'dan',
		'NLD',
		'eng',
		'fry',
		'FIN',
		'FRA',
		'de',	//	DEU
		'GRC',
		'HUN',
		'IND',
		'ISL',
		'ITA',
		'JPN',
		'KOR',
		'lat',
		'NOR',
		'persian',
		'pol',
		'por',
		'rum',
		'RUS',
		'slo',
		'ESP',
		'SWE',
		'swa',
		'wel');

	//	$sd_merge = array( 'nld', 'hol', 'grc', 'gre', 'hun', 'ung', 'jpn', 'jap', 'esp', 'spa');
	$sd_term_alt = array(
		'afr',
		'bul',
		'zh_CN',
		'czech',
		'dan',
		'hol',
		'eng',
		'fry',
		'FIN',
		'FRA',
		'de',	//	DEU
		'gre',
		'ung',
		'IND',
		'ISL',
		'ITA',
		'jap',
		'KOR',
		'lat',
		'NOR',
		'persian',
		'pol',
		'por',
		'rum',
		'RUS',
		'slo',
		'spa',
		'SWE',
		'swa',
		'wel');

	$sd_language = array(
		'Afrikaans',
		'Bulgarien',
		'Chinese',
		'Czech',
		'Danish',
		'Dutch',
		'English',
		'Frisiska',
		'Finnish',
		'French',
		'German',
		'Greek',
		'Hungarian',
		'Indonesian',
		'Islandska',
		'Italian',
		'Japanese',
		'Korean',
		'Latin',
		'Norwegian',
		'Persian',
		'Polish',
		'Portuguese',
		'Romanian',
		'Russian',
		'Slovakiska',
		'Spanish',
		'Swedish',
		'Swahili',
		'Walesiska');

	//	$sd_merge = array( 'nld', 'hol', 'grc', 'gre', 'hun', 'ung', 'jpn', 'jap', 'esp', 'spa');
	$sd_merge = array(
		'nld',
		'hol',
		//	'deu',
		//	'de',
		'grc',
		'gre',
		'hun',
		'ung',
		'jpn',
		'jap',
		'esp',
		'spa');
}

if ($freedict_series)
{
	$sd_name_prefix = "iDict";
	$sd_name_postfix = "fDict";
	$sd_term = array(
		'Afrikaans',
		'Croatian',
		'Czech',
		'Danish',
		'German',
		'English',
		'French',
		'Hungarian',
		'Irish',
		'Italian',
		'Japanese',
		'Khasi',
		'Latin',
		'Dutch',
		'Portuguese',
		'Scottish',
		'Serbo-Croat',
		'Slovak',
		'Spanish',
		'Swahili',
		'Swedish',
		'Turkish');
}

function sd_get_db($term, $path)
{
	$ret = array();

	exec("ls $path/*.db", $r);
	foreach ($r as $s)
	{
		if (strpos(strtolower($s), strtolower($term)) !== false)
			$ret[] = $s;
	}

	return $ret;
}

function fwrite_dbname($fp, $r)
{
	foreach ($r as $db_name)
	{
		$s = "$db_name\n";
		fwrite($fp, $s);
	}
}

//	bundles
if ($quick_series)
{
	//	foreach ($sd_term as $term)
	for ($i = 0; $i < count($sd_term); $i++)
	{
		$term1 = $sd_term[$i];
		$term2 = $sd_term_alt[$i];
		echo "$i:\tProcessing $term1 ($term2)\n";
		$r1 = sd_get_db($term1, $sd_db_path);
		$r2 = sd_get_db($term2, $sd_db_path);
		$intersect = array_intersect($r1, $r2);
		$diff1 = array_diff($r1, $r2);
		$diff2 = array_diff($r2, $r1);
		//	$r = $intersect + $diff;
		$r = array_merge($intersect, $diff1, $diff2);
		$count = count($r);
		$name = $sd_language[$i];
		//	print_r($r);
		//	echo "Name: $name\nCount: $count\n\n";
		if ($count > 1)
		{
			$fp = fopen("$sd_profile_path/quick-$term1-$term2.profile", 'wb');
			$s = "$sd_name_prefix - $name $sd_name_postfix\\n(From/To $count Languages/Dictionaries)\n";
			fwrite($fp, $s);
			fwrite_dbname($fp, $r);
			fclose($fp);
		}
	}
}

if ($quick_deluxe)
{
	exec("ls $sd_db_path/*.db", $r);
	$count = count($r);
	$name = "Deluxe";
	$fp = fopen("$sd_profile_path/quick-deluxe.profile", 'wb');
	$s = "$sd_name_prefix - $sd_name_postfix $name\\n(From/To $count Languages/Dictionaries)\n";
	fwrite($fp, $s);
	fwrite_dbname($fp, $r);
	fclose($fp);
}

if ($freedict_series)
{
	//	foreach ($sd_term as $term)
	for ($i = 0; $i < count($sd_term); $i++)
	{
		$term = $sd_term[$i];
		$r = sd_get_db($term, $sd_db_path);
		$count = count($r);
		//	print_r($r);
		//	echo "Name: $name\nCount: $count\n\n";
		if ($count > 1)
		{
			$fp = fopen("$sd_profile_path/freedict-$term.profile", 'wb');
			$s = "$sd_name_prefix - $term $sd_name_postfix\\n(From/To $count Languages/Dictionaries)\n";
			fwrite($fp, $s);
			fwrite_dbname($fp, $r);
			fclose($fp);
		}
		else
		{
			echo "abandon: $term\n";
		}
	}
}

if ($freedict_deluxe)
{
	exec("ls $sd_db_path/*.db", $r);
	$count = count($r);
	$name = "Deluxe";
	$fp = fopen("$sd_profile_path/freedict-deluxe.profile", 'wb');
	$s = "$sd_name_prefix - $sd_name_postfix $name\\n(From/To $count Languages/Dictionaries)\n";
	fwrite($fp, $s);
	fwrite_dbname($fp, $r);
	fclose($fp);
}

?>
