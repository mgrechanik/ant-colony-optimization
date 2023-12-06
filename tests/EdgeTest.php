<?php
/**
 * This file is part of the mgrechanik/ant-colony-optimization library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/ant-colony-optimization/blob/main/LICENSE.md
 * @link https://github.com/mgrechanik/ant-colony-optimization
 */

declare(strict_types=1);

namespace mgrechanik\aco\tests;

use mgrechanik\aco\classic\Edge;

class EdgeTest extends \PHPUnit\Framework\TestCase
{
    public function testPheromonFormula() {
        $edge = new Edge(100, 50);
        $this->assertEquals(100, $edge->getDistance());
        $this->assertEquals(50, $edge->getPheromone());
        
        $edge->setPheromone(70);
        $this->assertEquals(70, $edge->getPheromone());
    }
}
