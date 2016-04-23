<?php

namespace ShuffleParser;

/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 28/03/2016
 * Time: 18:20
 */
class PokemonData implements SingleParser
{
    public $dexNumber;
    public $typeIndex;
    public $abilityIndex;
    public $growthId;
    public $nameIndex;
    public $formeNameIndex;
    public $class;
    public $megaEvolutionIcons;
    public $speedups;
    public $megaIndexes = [];

    function __construct(VersionParser $versionParser, $index)
    {
        $this->versionParser = $versionParser;
        $line = $versionParser->getLine('PokemonData', $index);
        if (strlen($line) == 36) {
            $this->parseV13($line);
        } else {
            $this->parseV10($line);
        }
    }

    public function getName($lang='US') {
        return $this->versionParser->getMessage("messagePokemonList_{$lang}", $this->nameIndex);
    }

    public function getFormeName($lang='US') {
        return $this->formeNameIndex
            ? $this->versionParser->getMessage("messagePokemonList_{$lang}", $this->formeNameIndex)
            : '';
    }

    public function getAbility() {
        return new PokemonAbility($this->versionParser, $this->abilityIndex);
    }

    private function parseV10($line)
    {
        $this->dexNumber = readBits($line, 0, 0, 10);
        $this->typeIndex = readBits($line, 1, 3, 5);
        $this->abilityIndex = readBits($line, 2, 0, 7);
        $this->growthId = readBits($line, 2, 7, 4);
        $this->nameIndex = readBits($line, 4, 6, 11);
        if ($this->formeNameIndex = readBits($line, 6, 1, 8)) {
            $this->formeNameIndex += 768;
        }
        $this->class = readBits($line, 7, 5, 4);
        $this->megaEvolutionIcons = readBits($line, 8, 1, 7);
        $this->speedups = readBits($line, 9, 0, 7);

        if ($mega = readBits($line, 9, 7, 11)) {
            $this->megaIndexes[] = $mega;
        }
        if ($mega = readBits($line, 12, 0, 11)) {
            $this->megaIndexes[] = $mega;
        }
    }

    public function isPokemon()
    {
        return $this->class == 0;
    }
}