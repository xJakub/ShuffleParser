<?php

namespace ShuffleParser;

/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 28/03/2016
 * Time: 19:32
 */
interface SingleParser
{
    public function __construct(VersionParser $parser, $index);
}