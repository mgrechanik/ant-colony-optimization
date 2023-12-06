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

use Yoast\PHPUnitPolyfills\Helpers\AssertAttributeHelper;
use mgrechanik\aco\SppTask;
use mgrechanik\aco\AFinder;


class SppTaskTest extends \PHPUnit\Framework\TestCase
{
    use AssertAttributeHelper;
    
    public function testWrongPath() {
        $this->expectException(\LogicException::class);
        new SppTask(5, 5);
    }
    
    public function testWrongFrom() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('getNodes')->willReturn($this->getNodes());
        $this->expectException(\InvalidArgumentException::class);
        $task = new SppTask(10, 2);
        $task->initialize($finder);
    }
    
    public function testWrongTo() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('getNodes')->willReturn($this->getNodes());
        $this->expectException(\InvalidArgumentException::class);
        $task = new SppTask(2, 10);
        $task->initialize($finder);
    }    
    
    public function testInitializeGood() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('getNodes')->willReturn($this->getNodes());
        $task = new SppTask(1, 2);
        $task->initialize($finder);
        
        $this->assertEquals([1, 2], $this->getPropertyValue( $task, 'nodesStart' ));
    }  
    
    public function testFindPathLongFirstPath() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('getNodes')->willReturn($this->getNodes());
        $finder->method('makeNodeRandomChoice')->willReturn(0);
        
        
        $task = new SppTask(0, 3);
        $task->initialize($finder);
        $path = $task->findPath(0);
        
        $this->assertEquals([0,1,2,3], $path);
    }
    
    public function testLongFirstPathReversed() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('getNodes')->willReturn($this->getNodes());
        $finder->method('makeNodeRandomChoice')->willReturn(0);
        
        $task = new SppTask(3, 0);
        $task->initialize($finder);
        $path = $task->findPath(0);
        
        $this->assertEquals([3,2,1,0], $path);
    }  
    
    public function testLongFirstPathAnotherStart() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('getNodes')->willReturn($this->getNodes());
        $finder->method('makeNodeRandomChoice')->willReturn(0);
        
        $task = new SppTask(0, 3);
        $task->initialize($finder);
        $path = $task->findPath(3);
        
        $this->assertEquals([0 , 3], $path);
    }     
    
    public function testLongSecondPathAnotherStart() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('getNodes')->willReturn($this->getNodes());
        $finder->method('makeNodeRandomChoice')->willReturn(1);
        
        $task = new SppTask(0, 3);
        $task->initialize($finder);
        $path = $task->findPath(0);
        
        $this->assertEquals([0 , 2 , 3], $path);
    }  
    
    public function testLongSecondPath() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('getNodes')->willReturn($this->getNodes());
        $finder->method('makeNodeRandomChoice')->willReturn(1);
        
        $task = new SppTask(3, 0);
        $task->initialize($finder);
        $path = $task->findPath(3);
        
        $this->assertEquals([3,1,2,0], $path);
    } 
    
    public function testGetNodesStartRangeOne() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('getNodes')->willReturn($this->getNodes());  
        $task = new SppTask(0, 3);
        $task->initialize($finder);
        
        $range = $task->getNodesStartRange(1);
        $this->assertEquals([0, 3], $range);
    }
    
    public function testGetNodesStartRangeTwo() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('getNodes')->willReturn($this->getNodes());  
        $task = new SppTask(0, 3);
        $task->initialize($finder);
        
        $range = $task->getNodesStartRange(2);
        $this->assertEquals([0, 3], $range);
    }   
    
    public function testGetNodesStartRangeThree() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('getNodes')->willReturn($this->getNodes());  
        $task = new SppTask(0, 3);
        $task->initialize($finder);
        
        $range = $task->getNodesStartRange(3);
        $this->assertEquals([0, 3, 0, 3], $range);
    }    
    
    protected function getNodes() {
        return [
            0 => 100,
            1 => 200,
            2 => 300,
            3 => 400,
        ];
    }
    
}
