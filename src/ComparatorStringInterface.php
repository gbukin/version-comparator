<?php

namespace App;

interface ComparatorStringInterface
{
    public function update(string $newString): void;

    public function getLength(): int;

    public function getALength(): int;

    public function toString(): string;

    public function toArray(): array;

    public function getWeight(): float;
}