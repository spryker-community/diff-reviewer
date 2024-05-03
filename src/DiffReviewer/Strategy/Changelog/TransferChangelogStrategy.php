<?php

namespace DiffReviewer\DiffReviewer\Strategy\Changelog;

use DiffReviewer\DiffReviewer\Module\ModuleNameResolver;
use SebastianBergmann\Diff\Chunk;

class TransferChangelogStrategy implements \DiffReviewer\DiffReviewer\Strategy\StrategyInterface
{
    public function run($diff, array $changelogData): array
    {
        $moduleName = ModuleNameResolver::getModuleName($diff);
        foreach ($diff['diff']->chunks() as $chunk) {
            $processedChunk = $this->processChunk($chunk);
            $changelogData[$moduleName][$processedChunk['type']] = array_merge($changelogData[$moduleName][$processedChunk['type']] ?? [], $processedChunk['text'] ?? []);
            $changelogData[$moduleName]['releaseType'] = $this->resolveReleaseType($changelogData[$moduleName]);
        }

        return $changelogData;
    }

    public function isApplicable(array $diff): bool
    {
        if (!isset($diff['tags'])) {
            return false;
        }

        if (in_array('xml', $diff['tags'], true) && in_array('transfer-schema', $diff['tags'], true)) {
            return true;
        }

        return false;
    }

    protected function processChunk(Chunk $chunk): array
    {
        $transferName = '';
        $propertyName = '';
        $result = [];
        foreach ($chunk->lines() as $line) {
            if ($line->type() === \SebastianBergmann\Diff\Line::UNCHANGED || $line->type() === \SebastianBergmann\Diff\Line::ADDED) {
                if (preg_match('/<transfer name="([a-zA-Z]*)"/', $line->content(), $matches)) {
                    $transferName = $matches[1];
                }
            }
            if ($line->type() === \SebastianBergmann\Diff\Line::ADDED) {
                if (preg_match('/<property name="([a-zA-Z0-9]*)"/', $line->content(), $matches)) {
                    $propertyName = $matches[1];
                }
            }
            if ($transferName && $propertyName) {
                $result['text'][] = sprintf('Introduced `%sTransfer.%s` transfer field.', $transferName, $propertyName) . PHP_EOL;
                $propertyName = '';
            }
        }

        if (isset($result['text'])) {
            $result['type'] = 'Improvements';
        }

        return $result;
    }

    protected function resolveReleaseType(array $changeLogDataForModule): string
    {
        return 'minor';
    }
}
