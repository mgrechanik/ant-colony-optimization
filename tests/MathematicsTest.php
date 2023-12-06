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

use mgrechanik\aco\classic\Mathematics;

class MathematicsTest extends \PHPUnit\Framework\TestCase
{
    public function testPheromonFormula() {
        $m = new Mathematics();
        $this->assertEquals(64, $m->pheromonFormula(4, 2, 4, 2));
    }
    
    public function testEvaporationFormula() {
        $m = new Mathematics();
        $this->assertEquals(5, $m->evaporationFormula(10, 0.5));
    }    
    
    public function testIncrementPheromoneAmount() {
        $m = new Mathematics();
        $this->assertEquals(10, $m->incrementPheromoneAmount(100, 10));        
    }
    
    public function testRandomInt() {
        $m = new Mathematics();
        $number = $m->randomInt(10, 100);
        $this->assertGreaterThanOrEqual(10, $number);     
        $this->assertLessThanOrEqual(100, $number);
    }    
    
    public function testArrayShuffle() {
        $m = new Mathematics();
        $array = [0,1,2,3,4,5,6,7,8,9];
        $arrayCheck = $m->arrayShuffle($array);
        $this->assertSame(count($array), count($arrayCheck));
        $this->assertNotEquals($array, $arrayCheck);     
    }     
}
