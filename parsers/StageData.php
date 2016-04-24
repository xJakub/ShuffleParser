<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 24/04/2016
 * Time: 23:00
 */

namespace ShuffleParser;


class StageData implements SingleParser
{
    public $pokemonIndex;
    public $skyfallCount;
    public $time;
    public $hp;

    public $Srank;
    public $Arank;
    public $Brank;
    public $catchRate;
    public $bonusRate;
    public $repeatCoins;
    public $firstTimeCoins;
    public $expYield;
    public $trackId;
    public $backgroundId;
    public $extraHp;
    public $layoutIndex;
    public $defaultSetIndex;
    public $moves;

    public function __construct(VersionParser $versionParser, $index)
    {
        $this->versionParser = $versionParser;
        $line = $versionParser->getLine('StageData', $index);

        if (strlen($line) == 92) {
            // 3ds 1.3
            $this->parseV13($line);
        } else if (strlen($line) == 84) {
            // mobile
            $this->parseMobile($line);
        } else {
            // 3ds
            $this->parseV10($line);
        }
    }

    private function parseV13($line)
    {
        $this->pokemonIndex = readBits($line, 0, 0, 10);
        $this->skyfallCount = readBits($line, 1, 6, 4);
        $this->time = readBits($line, 2, 3, 15);
        $this->hp = readBits($line, 4, 0, 20);

        $this->Srank = readBits($line, 48, 2, 10);
        $this->Arank = readBits($line, 49, 4, 10);
        $this->Brank = readBits($line, 50, 6, 10);
        $this->catchRate = readBits($line, 52, 0, 7);
        $this->bonusRate = readBits($line, 52, 7, 7);
        $this->repeatCoins = readBits($line, 56, 7, 16);
        $this->firstTimeCoins = readBits($line, 60, 0, 16);
        $this->expYield = readBits($line, 64, 0, 16);
        $this->trackId = readBits($line, 72, 3, 16);
        $this->backgroundId = readBits($line, 88, 2, 8);
        $this->extraHp = readBits($line, 80, 0, 16);
        $this->layoutIndex = readBits($line, 82, 0, 16);
        $this->defaultSetIndex = readBits($line, 84, 0, 16);
        $this->moves = readBits($line, 86, 0, 8);

    }

    /**
     * @return PokemonData
     */
    public function getPokemon()
    {
        return $this->versionParser->getEntry('PokemonData', $this->pokemonIndex);
    }
}