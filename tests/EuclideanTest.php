<?php
/**
 * This file is part of the mgrechanik/ant-colony-optimization library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/ant-colony-optimization/blob/main/LICENCE.md
 * @link https://github.com/mgrechanik/ant-colony-optimization
 */

declare(strict_types=1);

namespace mgrechanik\aco\tests;

use mgrechanik\aco\distances\Euclidean;

class EuclideanTest extends \PHPUnit\Framework\TestCase
{
    public function testFormulaWithPythagoreanTriple() {
        $distanceObj = new Euclidean();
        $this->assertEquals(13, $distanceObj->distance(1, 1, 6, 13));
    }
}
