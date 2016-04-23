<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 28/03/2016
 * Time: 18:26
 */
use ShuffleParser\PokemonData;
use ShuffleParser\VersionParser;

include("binlib.php");
include("SingleParser.php");
include("VersionParser.php");
include("parsers/PokemonData.php");
include("parsers/PokemonAbility.php");

$versionParser = new VersionParser(['D:\3ds\shuffle\romfs1.2\dump']);

$pokesCount = $versionParser->getLinesCount('PokemonData');
for ($p=1; $p<$pokesCount; $p++) {
    $poke = new PokemonData($versionParser, $p);
    if ($poke->isPokemon()) {
        $ability = $poke->getAbility();

        echo "PokemonData #{$p}.\n";
        if ($poke->getFormeName()) {
            echo "  {$poke->getName()} ({$poke->getFormeName()})\n";
        } else {
            echo "  {$poke->getName()}\n";
        }
        echo "  Ability: {$ability->getName()} ({$ability->percentages[0]}%, {$ability->percentages[1]}%, {$ability->percentages[2]}%)\n";

        echo "\n";
    }
}