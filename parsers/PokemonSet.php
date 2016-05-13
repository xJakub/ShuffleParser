<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 13/05/2016
 * Time: 23:20
 */

namespace ShuffleParser;


class PokemonSet implements SingleParser
{
    public $pokemonIds;

    public function __construct(VersionParser $versionParser, $index)
    {
        $this->parser = $versionParser;
        $this->line = $versionParser->getLine('PokemonSet', $index);
        $this->pokemonIds = [];
        for ($i=0; $i<10; $i++) {
            $this->pokemonIds[] = readShort($this->line, 2*$i);
        }
    }

    public function getPokemon($index) {
        return $this->parser->getEntry('PokemonData', $this->pokemonIds[$index]);
    }
}