<?php

namespace test\test;

use App\Comparator;
use PHPUnit\Framework\TestCase;

class ComparatorTest extends TestCase
{
    public function testComparatorPairComparison(): void
    {
        $highVersion = '2.1.1';
        $lowVersion = '2.0.1';

        $this->assertTrue(Comparator::gt($highVersion, $lowVersion));
        $this->assertFalse(Comparator::gt($lowVersion, $highVersion));

        $version_1 = '3.0.3.2';
        $version_2 = '3.0.3.2.0';
        $this->assertTrue(Comparator::eq($version_1, $version_2));

        $highVersion = '7.34.2.1';
        $lowVersion = '6.49.5.3';
        $this->assertTrue(Comparator::lt($lowVersion, $highVersion));
        $this->assertFalse(Comparator::gt($lowVersion, $highVersion));

        $version_1 = '2.2.2';
        $version_2 = '2.2.2.1';
        $this->assertFalse(Comparator::gt($version_1, $version_2));

        $comparator = new Comparator();
        $comparator->pushVersion([$version_1, $version_2]);

        $this->assertSame($version_2, $comparator->getHighestVersion());
    }

    public function testComparatorMassComparison(): void
    {
        $comparator = new Comparator();
        $comparator->pushVersion(['1.0.1.1', '1.0.1.2', '1.0.1.3']);
        $this->assertSame('1.0.1.3', $comparator->getHighestVersion());
        $this->assertSame('1.0.1.1', $comparator->getLowestVersion());

        $comparator = new Comparator();
        $comparator->pushVersion(['100.1.1.3.4', '200.0.0.2']);
        $this->assertSame('200.0.0.2', $comparator->getHighestVersion());
        $this->assertSame('100.1.1.3.4', $comparator->getLowestVersion());

        $comparator = new Comparator();
        $comparator->pushVersion(['2.2.3', '2.2.3.0', '0.3.2.2', '2.2.4.0']);
        $this->assertSame('2.2.4.0', $comparator->getHighestVersion());
        $this->assertSame('0.3.2.2', $comparator->getLowestVersion());
    }


    public function testComparatorProducer(): Comparator
    {
        $comparator = new Comparator();
        $this->assertSame('', $comparator->getHighestVersion());

        return $comparator;
    }

    public static function caseProvider(): array
    {
        return [
            ['1.0.1.1', '1.0.1.1', true],
            ['2.0.1.1', '2.3.4.2', false],
            ['1.0.1.1', '2.0.1.1', true],
            ['6.1', '6.1', true]
        ];
    }

    /**
     * @depends testComparatorProducer
     * @dataProvider caseProvider
    */
    public function testComparatorMassComparisonWithUpdateSet($addingVersion, $highestVersion, $expected, $comparator): void
    {
        $comparator->pushVersion($addingVersion);
        $this->assertEquals($expected, $comparator->getHighestVersion() === $highestVersion);
    }
}
