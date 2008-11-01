<?php

$sd_name_prefix = "iDict";
$sd_name_postfix = "Quick";
$sd_db_path = "./data/db";
$sd_profile_path = "./data/profile";
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

//	foreach ($sd_term as $term)
for ($i = 0; $i < count($sd_term); $i++)
{
	$term1 = $sd_term[$i];
	$term2 = $sd_term_alt[$i];
	echo "$i:\tProcessing $term1 ($term2):\n";
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
		$fp = fopen("$sd_profile_path/$term1-$term2.profile", 'wb');
		$s = "$sd_name_prefix - $name $sd_name_postfix (From/To $count Languages/Dictionaries)\n";
		fwrite($fp, $s);
		foreach ($r as $db_name)
		{
			$s = "$db_name\n";
			fwrite($fp, $s);
		}
		fclose($fp);
	}
}

?>
