<?php

namespace ShuffleParser;
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 28/03/2016
 * Time: 19:34
 */
class VersionParser
{
    private $cache = [];
    private $messageCache = [];
    private $entryCache = [];
    private $version = "";
    private $dirs;

    function __construct($dirs)
    {
        $this->dirs = $dirs;

        if (preg_match("'1\\.([0-9]{1,2})(\\.([0-9]{1,2}))?(\\.([0-9]{1,2}))?'", $this->dirs[0], $match)) {
            $this->version = $match[0];
        }
    }

    function getVersion()
    {
        return $this->version;
    }

    function getLine($folder, $index)
    {
        if (!isset($this->cache[$folder])) {
            $this->cache[$folder] = dirToLines(findFile($this->dirs, $folder));
        }
        return $this->cache[$folder][$index];
    }

    function getLinesCount($folder) {
        if (!isset($this->cache[$folder])) {
            $this->cache[$folder] = dirToLines(findFile($this->dirs, $folder));
        }
        return count($this->cache[$folder]);
    }

    function getEntry($folder, $index) {
        $className = "ShuffleParser\\{$folder}";
        if (!isset($this->entryCache[$folder][$index])) {
            $this->entryCache[$folder][$index] = new $className($this, $index);
        }
        return $this->entryCache[$folder][$index];
    }

    function getAllEntries($folder) {
        $count = $this->getLinesCount($folder);
        $className = "ShuffleParser\\{$folder}";
        for ($i=0; $i<$count; $i++) {
            if (!isset($this->entryCache[$folder][$i])) {
                $this->entryCache[$folder][$i] = new $className($this, $i);
            }
        }
        ksort($this->entryCache[$folder]);
        return $this->entryCache[$folder];
    }

    function getMessage($folder, $index, $ln=null)
    {
        if (!isset($this->messageCache[$folder])) {
            $this->messageCache[$folder] = readTextsFromFile(findFile($this->dirs, 'messages/'.$folder.'.txt'));
        }

        $message = $this->messageCache[$folder][$index];
        if ($ln === null) {
            return $message;
        } else {
            return str_replace('\n', $ln, $message);
        }
    }
}