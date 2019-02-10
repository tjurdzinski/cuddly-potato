<?php

namespace App\Service\Parser;


use App\Service\StatsDTO;

class SubRipParser implements ParserInterface
{
    const TIME_PATTERN = '/\d{2}:\d{2}:\d{2},\d{3} --> \d{2}:\d{2}:\d{2},\d{3}/';
    const HAS_LETTERS_PATTERN = '/[a-zA-Z]/';

    /**
     * @param string $filePath
     * @return bool
     */
    public function supports(string $filePath): bool
    {
        $f = fopen($filePath, 'r');

        $lines = [];

        while ($line = fgets($f)) {
            $line = str_replace(["\r\n", "\r", "\n"], '', $line);

            if (empty($line)) {
                break;
            }

            $lines[] = $line;
        }

        fclose($f);

        if (!intval($lines[0])) {
            return false;
        }

        if (!preg_match(self::TIME_PATTERN, $lines[1])) {
            return false;
        }

        if (!preg_match(self::HAS_LETTERS_PATTERN, $lines[2])) {
            return false;
        }

        return true;
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
            $line = str_replace(["\r\n", "\r", "\n"], '', $line);

            if ($this->isNotSubtitle($line)) {
                continue;
            }

            $stats->addLine(strip_tags($line));
        }

        fclose($f);

        return $stats;
    }

    /**
     * @param string $line
     * @return bool
     */
    private function isNotSubtitle(string $line): bool
    {
        return empty($line) || (intval($line) && !preg_match(self::HAS_LETTERS_PATTERN, $line)) || preg_match(self::TIME_PATTERN, $line);
    }
}