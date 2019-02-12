<?php

namespace App\Service;

use App\Service\Parser\ParserFactory;

class StatsCalculator
{
    /**
     * @var ParserFactory
     */
    private $parserFactory;

    /**
     * @var bool
     */
    private $workOnCopy = false;

    /**
     * @var string
     */
    private $fileContent;

    public function __construct(ParserFactory $parserFactory)
    {
        $this->parserFactory = $parserFactory;
    }

    /**
     * @param string $filePath
     * @return StatsDTO
     */
    public function getStats(string $filePath): StatsDTO
    {
        $filePath = $this->fixEncoding($filePath);

        $parser = $this->parserFactory->getParser($filePath);

        $stats = new StatsDTO();

        /**
         * Why is StatsDTO passed to the parser?
         * We need to see the whole file in a loop, 
         * so as not to create another loop for making statistics, we collect them line by line in one loop.
         */
        $parser->parse($filePath, $stats);

        if ($this->workOnCopy) {
            unlink($filePath);
        }

        return $stats;
    }

    private function fixEncoding(string $filePath)
    {
        $this->fileContent = file_get_contents($filePath);

        if (!$this->isPolishFile()) {
            return $filePath;
        }

        if ($this->encodingIsCorrect()) {
            return $filePath;
        }

        $convertedContent = mb_convert_encoding($this->fileContent, 'utf-8', 'windows-1250');

        if ($this->encodingIsCorrect($convertedContent)) {
            $tempFilePath = tempnam(sys_get_temp_dir(), "cuddly-potato");
            file_put_contents($tempFilePath, $convertedContent);

            $this->workOnCopy = true;

            return $tempFilePath;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function isPolishFile(): bool
    {
        /**
         * Simple condition to check language of file
         */
        if (strpos($this->fileContent, 'nie') && strpos($this->fileContent, 'tak')) {
            return true;
        }

        return false;
    }

    /**
     * @param string|null $filePath
     * @return bool
     */
    private function encodingIsCorrect(string $filePath = null): bool
    {
        return (bool)strpos($filePath ?: $this->fileContent, 'się');
    }
}
