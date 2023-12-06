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

use Yoast\PHPUnitPolyfills\Helpers\AssertAttributeHelper;
use mgrechanik\aco\TspTask;
use mgrechanik\aco\AFinder;


class TspTaskTest extends \PHPUnit\Framework\TestCase
{
    use AssertAttributeHelper;
    
    public function testInitializeGood() {
        $task = $this->getTask();
        $this->assertEquals([0, 1, 2, 3], $this->getPropertyValue( $task, 'nodesStart' ));
    }    
    
    public function testNormalizePath() {
        $task = $this->getTask();
        $this->assertEquals([0, 1, 2, 3, 0], $task->normalizePath([0, 1, 2, 3]));
        $this->assertEquals([0, 1, 3, 2, 0], $task->normalizePath([1, 0, 2, 3]));
        $this->assertEquals([0, 1, 3, 2, 0], $task->normalizePath([1, 3, 2, 0]));
    }
    
    public function testFindPathFromZeroChoiceZero() {
        $task = $this->getFindTask(0);
        $path = $task->findPath(0);
        
        $this->assertEquals([0, 1, 2, 3, 0], $path);        
    }
    
    public function testFindPathFromOneChoiceZero() {
        $task = $this->getFindTask(0);
        $path = $task->findPath(1);
        
        $this->assertEquals([0, 1, 3, 2, 0], $path);        
    }    
    
    public function testFindPathFromThreeChoiceZero() {
        $task = $this->getFindTask(0);
        $path = $task->findPath(3);
        
        $this->assertEquals([0, 1, 2, 3, 0], $path);        
    }       
    
    public function testFindPathFromZeroChoiceOne() {
        $task = $this->getFindTask(1);
        $path = $task->findPath(0);
        
        $this->assertEquals([0, 1, 3, 2, 0], $path);        
    } 
    
    public function testFindPathFromThreeChoiceOne() {
        $task = $this->getFindTask(1);
        $path = $task->findPath(3);
        
        $this->assertEquals([0, 2, 1, 3, 0], $path);        
    }    
    
    public function testFindPathFromOneChoiceOne() {
        $task = $this->getFindTask(1);
        $path = $task->findPath(1);
        
        $this->assertEquals([0, 1, 2, 3, 0], $path);        
    }     
    
    protected function getTask() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('getNodes')->willReturn($this->getNodes());
        $task = new TspTask();
        $task->initialize($finder);
        return $task;
    }
    
    protected function getFindTask($return) {
        $finder = $this->createStub(AFinder::class);
        $finder->method('getNodes')->willReturn($this->getNodes());
        $finder->method('makeNodeRandomChoice')->willReturn($return);
        
        $task = new TspTask();
        $task->initialize($finder);
        return $task;
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
