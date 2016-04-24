<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 28/03/2016
 * Time: 18:26
 */
namespace ShuffleParser;

include("binlib.php");
include("SingleParser.php");
include("VersionParser.php");

foreach(glob('parsers/*.php') as $file) {
    include_once($file);
}

$versionParser = new VersionParser(['D:\3ds\shuffle\romfs1.3\dump']);

$pokesCount = $versionParser->getLinesCount('PokemonData');
for ($p=1; $p<$pokesCount; $p++) {
    /** @var PokemonData $poke */
    $poke = $versionParser->getEntry('PokemonData', $p);
    if ($poke->isPokemon()) {
        $ability = $poke->getAbility();

        echo "PokemonData #{$p}.\n";
        if ($poke->getFormeName()) {
            $name = "{$poke->getName()} ({$poke->getFormeName()})";
        } else {
            $name = "{$poke->getName()}";
        }
        $typeName = $poke->getType()->getName();

        echo "  $name, $typeName, Base {$poke->getAP()}, Dex #{$poke->dexNumber}\n";

        echo "  Ability: {$ability->getName()} ("
            ."{$ability->percentages[0]}%, {$ability->percentages[1]}%, {$ability->percentages[2]}%, "
            . ($ability->isDamage() ? "damage, x{$ability->modifiers[0]}, " : '')
            . ($ability->isGauge() ? "megabar, +{$ability->modifiers[0]}, " : '')
            . ($ability->isOther() ? "other, " : '')
            . $ability->getDescription()
            .")\n";

        echo "\n";
    }
}