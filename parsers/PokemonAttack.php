<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 24/04/2016
 * Time: 2:31
 */

namespace ShuffleParser;


class PokemonAttack implements SingleParser
{
    public function __construct(VersionParser $versionParser, $index)
    {
        $line = $versionParser->getLine('PokemonAttack', $index);

        $this->APs = [];
        for ($i=0; $i<strlen($line); $i++) {
            $this->APs[] = readByte($line, $i);
        }
    }

    static function getAP(VersionParser $versionParser, $basePowerId, $levelIndex=0) {
        /** @var PokemonAttack $entry */
        $entry = $versionParser->getEntry('PokemonAttack', $levelIndex);
        return $entry->APs[$basePowerId-1];
    }
}