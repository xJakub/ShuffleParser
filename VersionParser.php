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

    function getMessage($folder, $index)
    {
        if (!isset($this->messageCache[$folder])) {
            $this->messageCache[$folder] = readTextsFromFile(findFile($this->dirs, 'messages/'.$folder.'.txt'));
        }
        return $this->messageCache[$folder][$index];
    }
}