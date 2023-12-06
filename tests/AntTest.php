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

use mgrechanik\aco\classic\Ant;
use mgrechanik\aco\AFinder;
use mgrechanik\aco\Task;

class AntTest extends \PHPUnit\Framework\TestCase
{
    public function testPheromonFormula() {
        $finder = $this->createStub(AFinder::class);
        $ant = new Ant(1, $finder);
        
        $this->assertEquals(1, $ant->getNumber());        
    }
    
    public function testNewStart() {
        $finder = $this->createStub(AFinder::class);
        $ant = new Ant(1, $finder);
        $ant->newStart(99);
        $path = $ant->getPath();
        
        $this->assertEquals([99], $path);
        $this->assertEquals(0, $ant->getDistance());
    }

    public function testFindPathWrong() {
        $finder = $this->createStub(AFinder::class);
        $ant = new Ant(1, $finder);
        $this->expectException(\LogicException::class);
        $ant->findPath();
    }
    
    public function testFindPath() {
        $task = $this->createStub(Task::class);
        $task->method('findPath')->willReturn([1,2,3]);
        
        $finder = $this->createStub(AFinder::class);
        $finder->method('getTask')->willReturn($task);
        $finder->method('countDistance')->willReturn(900.0);        
        $ant = new Ant(1, $finder);
        $ant->newStart(99);
        $ant->findPath();
        
        $this->assertEquals([1, 2, 3], $ant->getPath());
        $this->assertEquals(900, $ant->getDistance());
    }    
}
