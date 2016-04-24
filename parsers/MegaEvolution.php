<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 24/04/2016
 * Time: 23:28
 */

namespace ShuffleParser;


class MegaEvolution implements SingleParser
{
    public function __construct(VersionParser $versionParser, $index)
    {
        $this->versionParser = $versionParser;
        $line = $versionParser->getLine('MegaEvolution', $index);
        $this->descriptionIndex = readShort($line, 2);
    }

    public function getDescription($lang='US') {
        return $this->versionParser->getMessage("messagePokedex_{$lang}", $this->descriptionIndex, ' ');
    }
}