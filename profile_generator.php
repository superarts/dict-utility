<?php

$quick_series = false;
$quick_deluxe = false;
$freedict_series = false;
$freedict_deluxe = false;
$dictd_series = true;

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

$data_dictd = array(
		array(
			'Vietnamese Dictd',
			'viet',
		),
		array(
			'Thesaurus',
			'thesaurus',
			'jargon',
			'vera',
			'elements',
			'foldoc',
			'smiley',
		),
		array(
			'Gazetteer',
			'gazetteer',
			'world02',
			'smiley',
		),
		array(
			'Bible & Devils',
			'devils',
			'hitchcock',
			'smiley',
		),
		array(
			'GNU Collaborative International Dictionary of English',
			'gcide',
			'smiley',
		),
		array(
			'WordNet',
			'wn',
			'smiley',
		),
		array(
			'Большой словарь',
			'1000pbio',
			'aviation',
			'beslov',
			'biology',
			'brok_and_efr',
			'ethnographic',
			'findict',
			'idioms',
			'mech',
			'ozhegov',
			'religion',
			'sc_abbr',
			'teo',
			'ushakov',
			'zhelezyaki',
		),
		array(
			'Russian Mova',
			'deutsch',
			'swedish',
			'engcom',
			'geology',
			'sc_abbr',
		),
		array(
			'English & Russian Sinyagin',
			'sinyagin_abbrev',
			'sinyagin_alexeymavrin',
			'sinyagin_business',
			'sinyagin_computer',
			'sinyagin_general',
			'smiley',
		),
		array(
			'English & Russian Sokrat',
			'sokrat',
			'smiley',
		),
		array(
			'English & Russian Korolew',
			'korolew',
			'smiley',
		),
		array(
			'English & Russian Dictd',
			'sokrat',
			'korolew',
		),
		array(
			'Belarusian Dictd',
			'compbe',
			'be',
		),
		array(
			'English Slovnyk',
			'en',
		),
		array(
			'Polish Slovnyk',
			'pl',
		),
		array(
			'Russian Slovnyk',
			'ru',
		),
		array(
			'Ukrainian Slovnyk',
			'uk',
		),
	);

if ($dictd_series)
{
	foreach ($data_dictd as $r)
	{
		$name = $r[0];
		$count = count($r);

		$filename = str_replace(' & ', '_', $name);
		$filename = str_replace(' ', '_', $filename);
		echo "processing $name\n";
		$fp = fopen("$sd_profile_path/$filename.profile", 'wb');

		$s = "$name\\n($count Databases)\n";
		fwrite($fp, $s);

		for ($i = 1; $i < count($r); $i++)
		{
			$s = "_" . $r[$i] . "_";
			$s = "ls $sd_db_path/*$s*.db";
			echo "\tlisting $s\n";

			$result = array();
			exec("$s", $result);

			//	print_r($result);
			fwrite_dbname($fp, $result);
			//fwrite($fp, $s);
		}
		fclose($fp);
	}
}

?>
