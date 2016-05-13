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

    printPokemonData($stage->getPokemon());
    echo "\n";

    printStageData($stage);
    echo "\n\n";

}

function printStageData(StageData $stage)
{
    $defaultPokemon = $stage->getDefaultSupportPokemon();
    $defaultPokemonCount = count($defaultPokemon);
    $extraPokemon = $stage->getExtraPokemon();

    echo "  --Stage data--\n";
    echo "  Background ID: {$stage->backgroundId}\n";
    echo "  HP: {$stage->hp}\n";

    if ($stage->moves) {
        echo "  Moves: {$stage->moves}\n";
    } else {
        echo "  Time: {$stage->time} sec\n";
    }
    echo "  Experience: +{$stage->expYield}\n";

    if ($stage->catchRate) {
        echo "  Catch rate: {$stage->catchRate}% ";
        if ($stage->moves) {
            echo "(+{$stage->bonusRate}% per every 3 remaining move)\n";
        } else {
            echo "(+{$stage->bonusRate}% per every 3 remaining seconds)\n";
        }
    } else {
        echo "  Catch rate: Not catchable\n";
    }
    echo "  Pokémon count: {$stage->skyfallCount} ({$defaultPokemonCount} elegible)\n";
    echo "\n";

    $defaultPokemonNames = [];
    foreach($defaultPokemon as $poke) {
        $defaultPokemonNames[] = $poke->getFullName();
    }
    $defaultPokemonNames = implode(', ', $defaultPokemonNames);
    echo "  Default Pokémon: {$defaultPokemonNames}\n";

    if ($extraPokemon) {
        $extraPokemonNames = [];
        foreach($extraPokemon as $poke) {
            $extraPokemonNames[] = $poke->getFullName();
        }
        $extraPokemonNames = implode(', ', $extraPokemonNames);
        echo "  Additional Pokémon (not from countdowns or maps): {$extraPokemonNames}\n";
    }

    if ($stage->layoutIndex) {
        echo "  This stage has got a predefined layout\n";
    }
    echo "  Coins reward: {$stage->firstTimeCoins} for the first time, " .
        "{$stage->repeatCoins} for every repeat\n";

}
function printPokemonData(PokemonData $poke) {
    echo "  --Pokémon data--\n";

    $name = $poke->getFullName();
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
        echo "  Mega-Bar: {$poke->megaEvolutionIcons}, {$poke->speedups} speedups available\n";
    }
}