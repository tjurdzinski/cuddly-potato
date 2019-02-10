<?php

namespace App\Service;

class StatsDTO
{
    const WORD_PATTERN = '/^[\p{L}\s]+$/u';
    const VISIBLE_PUNCTUATOR = '/[[:punct:]]/';

    /**
     * @var array
     */
    private $stats = [];

    /**
     * @param string $line
     */
    public function addLine(string $line): void
    {
        $words = explode(' ', preg_replace(self::VISIBLE_PUNCTUATOR, '', strtolower($line)));

        foreach ($words as $word) {
            if (preg_match(self::WORD_PATTERN, $word)) {
                $this->addWord($word);
            }
        }
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        arsort($this->stats);
        return $this->stats;
    }

    /**
     * @param string $word
     */
    private function addWord(string $word): void
    {
        if (array_key_exists($word, $this->stats)) {
            $this->stats[$word]++;
            return;
        }

        $this->stats[$word] = 1;
        return;
    }
}