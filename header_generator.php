<?php

require("sd_path.php");

/*
$sd_profile_path = "./data/profile";
$sd_script_path = "./data/script";
$sd_superdic_path = "../../../../iphone/superdic/data/db";
$sd_prj = "/Users/leo/prj";
$sd_profile_path = "$sd_prj/data/profile";
$sd_script_path = "$sd_prj/data/script";
$sd_superdic_path = "$sd_prj/iphone/superdic/data/db";
*/

function sd_get_db_info($dbname, $item)
{
	exec("sqlite3 data/idict_info.db 'select filename from info;'", $array_filename);
	exec("sqlite3 data/idict_info.db 'select bookname from info;'", $array_bookname);

	for ($i = 0; $i < count($array_filename); $i++)
	{
		$filename = $array_filename[$i];
		$bookname = $array_bookname[$i];
		if ((strpos($dbname, $filename) !== false) and (strpos($dbname, $bookname) !== false))
		{
			$ret = exec("sqlite3 data/idict_info.db 'select $item from info where filename=\"$filename\" and bookname=\"$bookname\";'");
			//	echo "*$filename--$bookname: $ret\n";
			return $ret;
		}
	}

	return "NA";
}

function sd_get_dict_macro($dictname)
{
	$i = strpos($dictname, "(");
	$ret = substr($dictname, 0, $i - 2);
	$ret = str_replace(".", "_", $ret);
	$ret = str_replace("-", "_", $ret);
	$ret = str_replace("/", "_", $ret);
	$ret = str_replace(" ", "_", $ret);

	return strtoupper($ret);
}

//	generate header
if (true)
{
	$fp_readme = fopen($sd_filename_readme, 'wb');
	exec("ls $sd_profile_path/*.profile", $array_profile);
	/*
	exec("ls $sd_profile_path/", $array_profile);
	print_r($array_profile);
	echo "xxx $sd_profile_path\n"; exit(0);
	*/
	//	exec("sqlite3 data/idict_info.db 'select filename from info;'", $array_filename);
	//	exec("sqlite3 data/idict_info.db 'select bookname from info;'", $array_bookname);

	//	print_r($array_bookname);
	//	print_r($array_profile);

	echo "#ifndef __DICTDATA_H\n";
	echo "#define __DICTDATA_H\n";
	echo "\n";

	$dict_array = array();
	foreach ($array_profile as $profilename)
	{
		$array_profile = file($profilename);
		$dictname = $array_profile[0];
		//	echo "xxxx $dictname\n"; exit(0);
		$dictname = substr($dictname, 0, -1);
		$dictmacro = sd_get_dict_macro($dictname);
		$dict_array[] = $dictmacro;

		$script_name = strtolower($dictmacro) . ".sh";
		$fp = fopen("$sd_script_path/$script_name", "wb");
		$s = "#!/bin/bash\n";
		fwrite($fp, $s);
		//$s = "rm $sd_superdic_path/*\n";
		$s = "mv $sd_superdic_path/* '$sd_db_path/'\n";
		fwrite($fp, $s);
		$s = "du -h $sd_db_path/\n";
		fwrite($fp, $s);
		$s = "du -h $sd_dbbak_path/\n";
		fwrite($fp, $s);

		echo "#ifdef $dictmacro\n";
		echo "\n";
		//	echo "$dictmacro - $profilename\n";
		//	echo "$dictname";
		echo "NSString* superdic_get_bundle(void)\n";
		echo "{\n";
		echo "	return [[NSString alloc] initWithString:@\"$dictname\"];\n";
		echo "}\n";
		echo "\n";
		$s = "Name: $dictname\n\nDictionaries:\n";
		fwrite($fp_readme, $s);

		echo "NSMutableArray* superdic_get_dbname(void)\n";
		echo "{\n";
		echo "	NSMutableArray* ret = [[NSMutableArray alloc] initWithObjects:\n";
		//	echo "		@\"\",\n";
		for ($index_profile = 1; $index_profile < count($array_profile); $index_profile++)
		{
			$dbname = $array_profile[$index_profile];
			$dbname = substr($dbname, strlen($sd_db_path) + 1, -1);
			//	$description = sd_get_db_info($dbname, 'description');
			//	$count = sd_get_db_info($dbname, 'count');
			//	$author = sd_get_db_info($dbname, 'author');

			echo "		@\"$dbname\",\n";

			$s = "mv '$sd_db_path/$dbname' '$sd_superdic_path/'\n";
			fwrite($fp, $s);
		}
		echo "		nil];\n";
		echo "	return ret;\n";
		echo "}\n";
		echo "\n";

		fclose($fp);
		exec("chmod +x $sd_script_path/$script_name");

		echo "NSMutableArray* superdic_get_description(void)\n";
		echo "{\n";
		echo "	NSMutableArray* ret = [[NSMutableArray alloc] initWithObjects:\n";
		//	echo "		@\"\",\n";
		$readme_filesize = 0;
		for ($index_profile = 1; $index_profile < count($array_profile); $index_profile++)
		{
			$dbname = $array_profile[$index_profile];
			$dbname = substr($dbname, 0, -1);
			$description = sd_get_db_info($dbname, 'description');
			$count = sd_get_db_info($dbname, 'count');
			//	$author = sd_get_db_info($dbname, 'author');

			echo "		@\"$description\",\n";
			$s = " * $description [$count Entries]\n";
			fwrite($fp_readme, $s);

			$readme_filesize += filesize($dbname);
		}

		echo "		nil];\n";
		echo "	return ret;\n";
		echo "}\n";
		echo "\n";

		echo "NSMutableArray* superdic_get_author(void)\n";
		echo "{\n";
		echo "	NSMutableArray* ret = [[NSMutableArray alloc] initWithObjects:\n";
		//	echo "		@\"\",\n";
		for ($index_profile = 1; $index_profile < count($array_profile); $index_profile++)
		{
			$dbname = $array_profile[$index_profile];
			$dbname = substr($dbname, 0, -1);
			//	$description = sd_get_db_info($dbname, 'description');
			//	$count = sd_get_db_info($dbname, 'count');
			$author = sd_get_db_info($dbname, 'author');

			echo "		@\"$author\",\n";
		}
		echo "		nil];\n";
		echo "	return ret;\n";
		echo "}\n";
		echo "\n";

		echo "NSMutableArray* superdic_get_count(void)\n";
		echo "{\n";
		echo "	NSMutableArray* ret = [[NSMutableArray alloc] initWithObjects:\n";
		//	echo "		@\"\",\n";
		$readme_count = 0;
		for ($index_profile = 1; $index_profile < count($array_profile); $index_profile++)
		{
			$dbname = $array_profile[$index_profile];
			$dbname = substr($dbname, 0, -1);
			//	$description = sd_get_db_info($dbname, 'description');
			$count = sd_get_db_info($dbname, 'count');
			//	$author = sd_get_db_info($dbname, 'author');

			echo "		@\"$count Entries\",\n";
			/*
			$s = "$count Entries.\n";
			fwrite($fp_readme, $s);
			*/

			$readme_count += $count;
		}
		echo "		nil];\n";
		echo "	return ret;\n";
		echo "}\n";
		echo "\n";

		echo "#endif	//	$dictmacro\n";
		echo "\n";

		$s = "\nTotally $readme_count Entries.\n";
		fwrite($fp_readme, $s);

		$readme_filesize_format = round($readme_filesize / 1024 / 1024);
		if ($readme_filesize_format == 0)
		{
			$readme_filesize_format = round($readme_filesize / 1024);
			$s = "Size: $readme_filesize_format" . "KB.\n__________\n\n";
		}
		else
		{
			$s = "Size: $readme_filesize_format" . "MB.\n__________\n\n";
		}
		fwrite($fp_readme, $s);
		/*
		for ($i = 0; $i < count($array_filename); $i++)
		{
			$filename = $array_filename[$i];
			$bookname = $array_bookname[$i];
			if ((strpos($dbname, $filename) !== false) and (strpos($dbname, $bookname) !== false))
			{
			}
		}
		*/
	}

	foreach ($dict_array as $s)
	{
		echo "//	#define $s\n";
	}
	echo "\n";

	echo "#endif // __DICTDATA_H";
	fclose($fp_readme);
}	//	header generator

?>
