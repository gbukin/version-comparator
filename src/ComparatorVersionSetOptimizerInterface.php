<?php

namespace App;

interface ComparatorVersionSetOptimizerInterface
{
    public function isOptimizeNeed(): bool;

    public function optimize(): array;

    public function getOptimizedLength(): int;
}