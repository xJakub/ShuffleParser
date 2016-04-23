<?php

function readNumber($con, $offset, $length) {
	$val = 0;
	for ($i=$length-1; $i>=0; $i--) {
		$val *= 256;
		$val += ord($con[$offset+$i]);
	}
	return $val;
}

function readByte($con, $offset) {
	return readNumber($con, $offset, 1);
}

function readShort($con, $offset) {
	return readNumber($con, $offset, 2);
}

function readInt($con, $offset) {
	return readNumber($con, $offset, 4);
}

function readFloat($con, $offset) {
	$result = unpack('f', substr($con, $offset, 4));
    return $result[1];
}
function readSignedInt($con, $offset) {
	$result = unpack('l', substr($con, $offset, 4));
    return $result[1];
}

function readBits($con, $offset, $bitsOffset, $bitsCount=null) {
    $result = readInt($con, $offset);
    $result >>= $bitsOffset;

    if ($bitsCount && $bitsCount != 32) {
        $result &= (1 << $bitsCount) - 1;
    }
    return $result;
}

function readOffsets($con, $offsets) {
	$ret = array();
	foreach($offsets as $key => $offset) {
		$length = 1;
		if (is_array($offset)) {
			list($offset, $length) = $offset;
		}
		$ret[$key] = readNumber($con, $offset, $length);
	}
	return $ret;
}

function readTextsFromFile($file) {
	return explode("\n", str_replace("\r","",encodeToUtf8(file_get_contents($file))));
}

function readTextsIntoRows($file, $rows, $key, $indexKey = null) {
	$texts = readTextsFromFile($file);
	
	foreach($rows as $index => &$row) {
		if ($indexKey !== null) {
			$index = $row[$indexKey];
		}
		$row[$key] = trim($texts[$index]);
	}
		
	return $rows;
}

function rowsToSql($table, $rows) {

	mysql_set_charset('utf-8');

	$fields = Array();
	$fieldsize = Array();

	foreach($rows as $row) {
		
		foreach($row as $key => $val) {
			
			if (!isset($fields[$key])) {
				$fields[$key] = $val;
			}
			elseif (is_int($val) && is_int($fields[$key])) {
				$field = $fields[$key];
				$fields[$key] = max($val, $field);
			}
			else {
				if (strlen($val) > strlen($fields[$key])) $fields[$key] = $val."";
				if (is_array($val)) {
					var_dump($row);
					exit;
					}
			}
			
		}
	}
	ksort($fields);
	$sql = "";
	foreach($fields as $key => $val) {
		if ($val === NULL) {
			unset($fields[$key]);
		}
		else {
			$sql .= "-- ALTER TABLE $table ADD $key ";
			if (is_int($val)) {
				$sql .=  "INT";
			}
			else if (strlen($val) < 10) {
				$sql .=  "CHAR(32)";
			}
			else if (strlen($val) < 100) {
				$sql .=  "VARCHAR(255)";
			}
			else {
				$sql .=  "TEXT";
			}
			$sql .=  ";\n";
		}
	}

	$sql .= "\n\n\nTRUNCATE TABLE $table;\n";

	foreach($rows as $index => $row) {
		if (!count($row)) continue;
		$sql .= "insert into $table (".implode(",",array_keys($row)).") values (";
		
		foreach($row as $key=>$val) {
			$sql .= "'".mysql_real_escape_string($val)."', ";
		}
		$sql = substr($sql, 0, -2);
		
		$sql .= ");\n";
		
	}
	
	return $sql;
}

function encodeToUtf8($string) {
     return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
}

function dirToLines($dir, $cache=null) {
    @mkdir("caches");
    $md5 = md5($dir);
    if ($cache!==false && !$_GET['nocache'] && file_exists("caches/$md5")) {
        return unserialize(file_get_contents("caches/$md5"));
    }
	$ret = array();
	foreach(glob("$dir/*") as $file) {
		$ret[] = file_get_contents($file);
	}
    file_put_contents("caches/$md5", serialize($ret));
	return $ret;
}

function findFile($dirs, $target) {
    foreach($dirs as $dir) {
        if (file_exists("$dir/$target")) {
            return "$dir/$target";
        }
    }
}

function findAllFiles($dirs, $target) {
    $result = array();
    foreach($dirs as $dir) {
        if (file_exists("$dir/$target")) {
            $result[] = "$dir/$target";
        }
    }
    return $result;
}