<?php

namespace App\Service\Parser;


class ParserFactory
{
    /**
     * @var array|ParserInterface[]
     */
    private $parsers = [];

    public function __construct(iterable $parsers)
    {
        $this->parsers = $parsers;
    }

    public function getParser(string $filePath) {
        foreach ($this->parsers as $parser) {
            if($parser->supports($filePath)) {
                return $parser;
            }
        }

        throw new \InvalidArgumentException('Invalid file format.');
    }
}