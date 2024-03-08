<?php

namespace App;

class Comparator
{
    private
    array $rawVersionStash = [];
    private
    array $optimizedVersionStash = [];
    private
    int $optimizedLength;

    /**
     * @var ComparatorString[]
     */
    private array $versionStash = [];
    private array $versionWeightStash;

    private bool $compared;

    public function __construct()
    {
        $this->compared = false;
    }

    public function getHighestVersion(bool $original = true): string
    {
        if (!$this->compared) $this->processVersionsSet();

        $highestWeight = max($this->versionWeightStash);
        $highestWeightKey = array_search($highestWeight, $this->versionWeightStash);

        if ($original)
            return $this->rawVersionStash[$highestWeightKey];
        else
            return $this->versionStash[$highestWeightKey]->toString($original);
    }

    public function getLowestVersion(bool $original = true): string
    {
        if (!$this->compared) $this->processVersionsSet();

        $lowestWeight = min($this->versionWeightStash);
        $lowestWeightKey = array_search($lowestWeight, $this->versionWeightStash);

        if ($original)
            return $this->rawVersionStash[$lowestWeightKey];
        else
            return $this->versionStash[$lowestWeightKey]->toString($original);
    }

    /**
     * @param string $version_a
     * @param string $operator - lt|gt|eq
     * @param string $version_b
     * @return bool
     */
    public static function compare(string $version_a, string $operator, string $version_b): bool
    {
        return self::$operator($version_a, $version_b);
    }

    public static function gt(string $version_a, $version_b): bool
    {
        [$version_1, $version_2] = self::prepareVersion($version_a, $version_b);

        return $version_1->getWeight() > $version_2->getWeight();
    }

    public static function eq(string $version_a, string $version_b): bool
    {
        [$version_1, $version_2] = self::prepareVersion($version_a, $version_b);

        return $version_1->getWeight() === $version_2->getWeight();
    }

    public static function lt(string $version_a, string $version_b): bool
    {
        [$version_1, $version_2] = self::prepareVersion($version_a, $version_b);

        return $version_1->getWeight() < $version_2->getWeight();
    }

    private static function prepareVersion(string $version_a, string $version_b): array
    {
        [$version_1, $version_2] = (new ComparatorVersionSetOptimizer([$version_a, $version_b]))->optimize();
        self::adaptVersion($version_1, $version_2);

        return [$version_1, $version_2];
    }

    private static function adaptVersion(string &$version_a, string &$version_b): void
    {
        $version_1 = new ComparatorString($version_a);
        $version_2 = new ComparatorString($version_b);

        $length = max($version_1->getALength(), $version_2->getALength());

        $version_1->fillToLength($length);
        $version_2->fillToLength($length);

        $version_a = $version_1;
        $version_b = $version_2;
    }

    public function pushVersion(array|string $versions): void
    {
        $this->compared = false;

        if (is_array($versions)) {
            $this->rawVersionStash = array_merge($this->rawVersionStash, $versions);
        } else {
            $this->rawVersionStash[] = $versions;
        }
    }

    private function processVersionsSet(): void
    {
        $optimizeResult = $this->optimizeSet($this->rawVersionStash);

        $this->optimizedVersionStash = $optimizeResult['optimized_set'];
        $this->optimizedLength = $optimizeResult['optimized_length'];

        $this->pushComparatorString();
    }

    private function optimizeSet(array $set): array
    {
        $optimizer = new ComparatorVersionSetOptimizer($set);

        return [
            'optimized_set' => $optimizer->optimize(),
            'optimized_length' => $optimizer->getOptimizedLength()
        ];
    }

    private function pushComparatorString(): void
    {
        foreach ($this->optimizedVersionStash as $version) {
            $comparatorVersion = new ComparatorString($version);

            $comparatorVersion->fillToLength($this->optimizedLength);

            $this->versionStash[] = $comparatorVersion;
            $this->versionWeightStash[] = $comparatorVersion->getWeight();
        }
    }
}