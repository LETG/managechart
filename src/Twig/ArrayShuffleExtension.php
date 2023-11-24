<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ArrayShuffleExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('shuffle', [$this, 'shuffleArray']),
        ];
    }

    public function shuffleArray($array): array
    {
        shuffle($array);
        return $array;
    }
}
