<?php

namespace App;

class ComparatorString implements ComparatorStringInterface
{
    private const string SEPARATOR = '.';

    private float $weight = -1;
    private string $dirtyString = '';

    public function __construct(private string $string)
    {
        $this->dirtyString = $this->string;
    }

    public function update(string $newString): void
    {
        $this->string = $newString;
    }

    public function getLength(): int
    {
        return strlen($this->string);
    }

    public function getALength(bool $original = false): int
    {
        return count($this->toArray($original));
    }

    public function toString(bool $original = false): string
    {
        return $original ? $this->string : $this->dirtyString;
    }

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

        foreach ($reversedVersionParts as $iteration => $step) {
            if (strlen($step) > 1) {
                $m_step = $step / 10;
            } else {
                $m_step = $step;
            }

            if ((int)$m_step === 0) {
                if (array_key_exists($iteration - 1, $reversedVersionParts))
                    $resultWeight -= pow($reversedVersionParts[$iteration - 1], $iteration + 1);
            } else {
                $resultWeight += pow($m_step, $iteration + 1);
            }
        }

        $resultWeight += $this->zeroAmount();

        return $resultWeight;
    }

    public function fillToLength(int $length, mixed $value = '0'): void
    {
        $filledArray = array_merge($this->toArray(original: true), array_fill(0, $length - $this->getALength(original: true), $value));

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