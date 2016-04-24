<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 23/04/2016
 * Time: 14:43
 */

namespace ShuffleParser;


class PokemonAbility implements SingleParser
{
    public $nameIndex;
    public $descIndex;
    public $percentages = [];
    public $modifiers = [];
    public $type;
    private $versionParser;

    public function __construct(VersionParser $versionParser, $index)
    {
        $this->versionParser = $versionParser;
        $line = $versionParser->getLine('PokemonAbility', $index);

        if (strlen($line) == 32) {
            $this->parseV13($line);
        } else {
            $this->parseV10($line);
        }
    }

    private function parseV10($line)
    {
        $this->modifiers = [
            readFloat($line, 0)
        ];
        $this->type = readByte($line, 4);
        $this->percentages[] = readByte($line, 5);
        $this->percentages[] = readByte($line, 6);
        $this->percentages[] = readByte($line, 7);
        $this->nameIndex = readByte($line, 8);
        $this->descIndex = readByte($line, 9);
    }

    private function parseV13($line)
    {
        $this->parseV10(substr($line, 16));

        $this->modifiers = [
            readFloat($line, 0),
            readFloat($line, 4),
            readFloat($line, 8),
            readFloat($line, 12),
            readFloat($line, 16),
        ];
    }

    public function isDamage() {
        return $this->type == 0;
    }
    public function isOther() {
        return $this->type == 1;
    }
    public function isGauge() {
        return $this->type == 2;
    }

    function getName($lang='US') {
        return $this->versionParser->getMessage("messagePokedex_{$lang}", $this->nameIndex);
    }
    function getDescription($lang='US') {
        return $this->versionParser->getMessage("messagePokedex_{$lang}", $this->descIndex, ' ');
    }
}