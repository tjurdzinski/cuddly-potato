<?php

namespace App\Service\Parser;


use App\Service\StatsDTO;

interface ParserInterface
{
    /**
     * @param string $filePath
     * @return bool
     */
    public function supports(string $filePath):bool;

    /**
     * @param string $filePath
     * @param StatsDTO $stats
     * @return StatsDTO
     */
    public function parse(string $filePath, StatsDTO $stats): StatsDTO;
}