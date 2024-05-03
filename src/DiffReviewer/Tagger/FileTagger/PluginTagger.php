<?php

namespace DiffReviewer\DiffReviewer\Tagger\FileTagger;

use SebastianBergmann\Diff\Diff;
use DiffReviewer\DiffReviewer\Tagger\FileTaggerInterface;

class PluginTagger implements FileTaggerInterface
{
    public function tag(Diff $file, array $tags): array
    {
        $from = $file->from();

        if (str_ends_with($from, 'Plugin.php')) {
            $tags[] = 'plugin';
        }

        if (str_ends_with($from, 'PluginInterface.php')) {
            $tags[] = 'plugin-interface';
        }

        return $tags;
    }
}
