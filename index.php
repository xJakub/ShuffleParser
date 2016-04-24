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

/** @var StageData $stage */
foreach($versionParser->getAllEntries('StageData') as $index => $stage) {
    echo "StageData #{$index}.\n";

    $poke = $stage->getPokemon();

    echo "--Pokémon data--\n";

    if ($poke->getFormeName()) {
        $name = "{$poke->getName()} ({$poke->getFormeName()})";
    } else {
        $name = "{$poke->getName()}";
    }
    $typeName = $poke->getType()->getName();

    echo "  $name, $typeName, Base {$poke->getAP()}, Dex #{$poke->dexNumber}\n";

    if ($poke->isPokemon()) {
        $ability = $poke->getAbility();;

        echo "  Ability: {$ability->getName()} ("
            ."{$ability->percentages[0]}%, {$ability->percentages[1]}%, {$ability->percentages[2]}%, "
            . ($ability->isDamage() ? "damage, x{$ability->modifiers[0]}, " : '')
            . ($ability->isGauge() ? "megabar, +{$ability->modifiers[0]}, " : '')
            . ($ability->isOther() ? "other, " : '')
            . $ability->getDescription()
            .")\n";
    } else if ($poke->isMegaEvolution()) {
        $ability = $poke->getAbility();;

        echo "  Ability: " . $ability->getDescription() ."\n";
    }

    echo "\n";
}