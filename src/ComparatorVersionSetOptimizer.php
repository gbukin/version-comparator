<?php

namespace App;

class ComparatorVersionSetOptimizer implements ComparatorVersionSetOptimizerInterface
{
    private int $optimizedLength = 0;

    public function __construct(private array $versionSet)
    {
    }

    public function isOptimizeNeed(): bool
    {
        if (count($this->versionSet) < 2) return false;

        $sortedSet = $this->versionSet;
        sort($sortedSet);

        $highestLength = $this->getVersionPartAmount($sortedSet[0]);
        $secondHighestLength = $this->getVersionPartAmount($sortedSet[1]);

        if (in_array($highestLength, [$secondHighestLength, $secondHighestLength + 1])) {
            $this->optimizedLength = $this->getVersionPartAmount($sortedSet[0]);
            return false;
        }

        $this->optimizedLength = $secondHighestLength;

        return true;
    }

    public function optimize(): array
    {
        if (count($this->versionSet) === 1) {
            $this->optimizedLength = $this->getVersionPartAmount($this->versionSet[0]);
        }

        if ($this->isOptimizeNeed()) {
            foreach ($this->versionSet as $key => $version) {
                if ($this->getVersionPartAmount($version) > $this->optimizedLength) {
                    $this->versionSet[$key] = $this->spliceVersion($version);
                }
            }
        }

        return $this->versionSet;
    }

    public function getOptimizedLength(): int
    {
        return $this->optimizedLength;
    }

    private function getVersionPartAmount(string $version): int
    {
        return count(explode('.', $version));
    }

    private function spliceVersion(string $version): string
    {
        return implode('.', array_slice(explode('.', $version), 0, $this->optimizedLength));
    }
}