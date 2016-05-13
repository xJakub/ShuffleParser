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

    public function getFullName($lang='US') {
        if ($this->getFormeName()) {
            $name = "{$this->getName($lang)} ({$this->getFormeName($lang)})";
        } else {
            $name = "{$this->getName($lang)}";
        }
        return $name;
    }

    /**
     * @return PokemonAbility|MegaEvolution
     */
    public function getAbility() {
        if ($this->isPokemon()) {
            return $this->versionParser->getEntry('PokemonAbility', $this->abilityIndex);
        } else if ($this->isMegaEvolution()) {
            return $this->versionParser->getEntry('MegaEvolution', $this->abilityIndex);
        }
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

    private function parseV13($line)
    {
        $this->dexNumber = readBits($line, 0, 0, 10);
        $this->typeIndex = readBits($line, 1, 3, 5);
        $this->abilityIndex = readBits($line, 2, 0, 7);
        $this->growthId = readBits($line, 3, 0, 4);
        $this->nameIndex = readBits($line, 6, 5, 11);
        if ($this->formeNameIndex = readBits($line, 8, 0, 8)) {
            $this->formeNameIndex += 768;
        }
        $this->class = readBits($line, 9, 4, 3);
        $this->megaEvolutionIcons = readBits($line, 10, 0, 7);
        $this->speedups = readBits($line, 10, 7, 7);

        if ($mega = readBits($line, 12, 0, 11)) {
            $this->megaIndexes[] = $mega;
        }
        if ($mega = readBits($line, 13, 3, 11)) {
            $this->megaIndexes[] = $mega;
        }
    }

    public function isPokemon()
    {
        return $this->class == 0;
    }

    /**
     * @return PokemonType
     */
    public function getType()
    {
        return $this->versionParser->getEntry('PokemonType', $this->typeIndex);
    }

    public function getAP($levelIndex=0) {
        return PokemonAttack::getAP($this->versionParser, $this->growthId, $levelIndex);
    }

    public function isMegaEvolution()
    {
        return $this->class == 2;
    }
}