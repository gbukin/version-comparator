<?php

namespace App;

class ComparatorString implements ComparatorStringInterface
{
    private const string SEPARATOR = '.';

    private float $weight = -1;
    private string $dirtyString = '';

    public function __construct(private readonly string $string)
    {
        $this->dirtyString = $this->string;
    }

    public function getALength(bool $original = false): int
    {
        return count($this->toArray($original));
    }

    public function toString(bool $original = false): string
    {
        return $original ? $this->string : $this->dirtyString;
    }

    /**
     * @param bool $original
     * @return string[]
     */
    public function toArray(bool $original = false): array
    {
        return explode(self::SEPARATOR, $original ? $this->string : $this->dirtyString);
    }

    public function getWeight(): float
    {
        if ($this->weight < 0) {
            $this->weight = $this->calcWeight();
        }

        return $this->weight;
    }

    private function calcWeight(): float
    {
        $resultWeight = 1;
        $reversedVersionParts = array_reverse($this->toArray());

        $modifier = 10 * count($this->toArray());

        /** @var int $iteration */
        foreach ($reversedVersionParts as $iteration => $step) {
            if ($step > 0) {
                if (strlen($step) === 1) {
                    $resultWeight += pow((int)$step, M_E) / $modifier;
                } elseif (strlen($step) === 2) {
                    $resultWeight += tanh((int)$step / 100);
                } else {
                    $resultWeight += (tanh((int)$step / 100) * 1000) / $modifier;
                }
            } else {
                $resultWeight -= $iteration;
            }

            $modifier /= 10;
        }

        return $resultWeight;
    }

    public function fillToLength(int $length, mixed $value = '0'): void
    {
        /** @var string[] $filledArray */
        $filledArray = array_merge(
            $this->toArray(original: true),
            array_fill(
                0,
                $length - $this->getALength(original: true),
                $value
            )
        );

        $this->dirtyString = implode(self::SEPARATOR, $filledArray);
    }

    private function zeroAmount(): int
    {
        $amount = 0;

        foreach ($this->toArray() as $versionPart) {
            if ($versionPart == 0) $amount += 1;
        }

        return $amount;
    }
}