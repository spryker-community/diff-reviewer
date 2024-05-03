<?php

namespace DiffReviewer\DiffReviewer\Strategy\Changelog;

use DiffReviewer\DiffReviewer\Module\ModuleNameResolver;
use SebastianBergmann\Diff\Chunk;
use SebastianBergmann\Diff\Diff;

class PluginChangelogStrategy implements \DiffReviewer\DiffReviewer\Strategy\StrategyInterface
{
    public function run($diff, array $changelogData): array
    {
        $moduleName = ModuleNameResolver::getModuleName($diff);
        if (in_array('new', $diff['tags'])) {
            $result = $this->processNew($diff['diff']);
            $changelogData[$moduleName]['Improvements'] = array_merge($changelogData[$moduleName]['Improvements'] ?? [], $result);
            $changelogData[$moduleName]['releaseType'] = $this->resolveReleaseType($changelogData[$moduleName], $changelogData[$moduleName]['releaseType']);
        }


        return $changelogData;
    }

    public function isApplicable(array $diff): bool
    {
        if (!isset($diff['tags'])) {
            return false;
        }

        if (in_array('php', $diff['tags'], true) && in_array('plugin', $diff['tags'], true)) {
            return true;
        }

        return false;
    }

    protected function processNew(Diff $diff): array
    {
        $parts = explode('/', $diff->to());
        $pluginName = end($parts);
        $pluginName = str_replace('.php', '', $pluginName);
        return [
            sprintf('Introduced `%s` plugin.', $pluginName) . PHP_EOL,
        ];
    }

    protected function resolveReleaseType(array $changeLogDataForModule, string $existingType): string
    {
        return 'minor';
    }
}
