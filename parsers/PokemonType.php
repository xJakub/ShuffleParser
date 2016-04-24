<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 24/04/2016
 * Time: 2:11
 */

namespace ShuffleParser;


class PokemonType implements SingleParser
{
    public function __construct(VersionParser $versionParser, $index)
    {
        $this->versionParser = $versionParser;
        $line = $versionParser->getLine('PokemonType', $index);
        $this->number = readByte($line, 4);
        $this->nameIndex = readByte($line, 6);
    }

    function getName($lang='US') {
        return $this->versionParser->getMessage("messagePokemonType_{$lang}", $this->nameIndex);
    }

    static function getByNumber(VersionParser $versionParser, $number) {
        /** @var PokemonType $entry */
        foreach($versionParser->getAllEntries('PokemonType') as $entry) {
            if ($entry->number == $number) {
                return $entry;
            }
        }
    }
}