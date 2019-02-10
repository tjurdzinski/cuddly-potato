<?php

namespace App\Service\Parser;


use App\Service\StatsDTO;

class MicroDvdParser implements ParserInterface
{
    const ONLY_TRANSLATION_PATTERN = '/}(?\'line\'.[^{}]*)$/m';
    const MICRO_DVD_LINE_PATTERN = '/\{[1-9]*\}\{[1-9]*\}.*/';

    /**
     * @param string $filePath
     * @return bool
     */
    public function supports(string $filePath): bool
    {
        $f = fopen($filePath, 'r');
        $firstLine = fgets($f);
        fclose($f);

        if (preg_match(self::MICRO_DVD_LINE_PATTERN, $firstLine)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $filePath
     * @param StatsDTO $stats
     * @return StatsDTO
     */
    public function parse(string $filePath, StatsDTO $stats): StatsDTO
    {
        $f = fopen($filePath, 'r');

        while ($line = fgets($f)) {
            $matches = [];

            /**
             * Extra action after text analysis
             */
            $line = str_replace(['|', '{y:i}'], ' ', $line);

            if (preg_match_all(self::ONLY_TRANSLATION_PATTERN, $line, $matches, PREG_SET_ORDER)) {
                $line = $matches[0]['line'];
                $line = str_replace(["\r\n", "\r", "\n"], '', $line);

                $stats->addLine($line);
            }

        }

        return $stats;
    }
}