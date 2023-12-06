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

use mgrechanik\aco\Manager;
use mgrechanik\aco\classic\Finder;
use Yoast\PHPUnitPolyfills\Helpers\AssertAttributeHelper;
use mgrechanik\aco\AFinder;
use mgrechanik\aco\MathematicsInterface;
use mgrechanik\aco\Task;
use LogicException;
use InvalidArgumentException;
use Exception;

class FinderTest extends \PHPUnit\Framework\TestCase
{
    use AssertAttributeHelper;
    
    public function testCreating() {
        $finder = $this->getClassicFinder();
        
        $this->assertInstanceOf(AFinder::class, $finder);
        $this->assertInstanceOf(MathematicsInterface::class, $finder->getMathematics());
        $this->assertInstanceOf(Manager::class, $finder->getManager());
        $this->assertInstanceOf(Task::class, $finder->getTask());        
    }
    
    public function testRunWrongIteration() {
        $finder = $this->getClassicFinder();
        $this->expectException(Exception::class);
        $finder->run([], 0);
    }
    
    public function testRunWrongTaskInitialization() {
        $finder = new Finder();
        $finder->setMathematics($this->createStub(MathematicsInterface::class));        
        $this->expectException(Exception::class);
        $finder->run([], 1);
    }    
    
    public function testRunWrongMathInitialization() {
        $finder = new Finder();
        $finder->setTask($this->createStub(Task::class));
        $this->expectException(Exception::class);
        $finder->run([], 1);
    }     
    
    public function testRunWrongMatrix() {
        $finder = $this->getClassicFinder();
        $this->expectException(Exception::class);
        $finder->run([], 1);
    }    
    
    public function testRunCorrect() {
        $finder = $this->getClassicFinder();
        $res = $finder->run($this->getMatrix(), 1);
        
        $this->assertArrayHasKey('distance', $res);
        $this->assertArrayHasKey('path', $res);
        $this->assertNotEmpty($finder->getHistory());
        $this->assertCount(4, $finder->getNodes());
        $this->assertCount(4, $finder->getEdges());
        $this->assertNotEquals($finder->getC(), $finder->getPheromoneOnEdge(0, 1));
        $this->assertNotEmpty($finder->getTimeFromStart());
        $this->assertEquals(287, $finder->getDistanceOnEdge(1, 2));
        $this->assertEquals($finder->getClosedPathValue(), $finder->getDistanceOnEdge(1, 20));
        $this->assertEquals(0, $finder->getPheromoneOnEdge(1, 20));
    }      
    
    public function testRunDistanceWrong() {
        $finder = $this->getClassicFinder();
        $finder->run($this->getMatrix(), 1);
        $this->expectException(Exception::class);
        
        $finder->getDistanceOnEdge(1, 1);
    }    

    public function testSettersGood() {
        $finder = $this->getClassicFinder();
        $finder->setIterations(2);
        $finder->setClosedPathValue(1000);
        $finder->setM(10);
        $finder->setMPercent(100);
        
        $finder->setAlpha(1.1);
        $finder->setBeta(1.1);
        $finder->setP(2.1);
        $finder->setC(2.2);
        $finder->setQ(110);
        
        $this->assertEquals(2, $this->getPropertyValue( $finder, 'iterations' ));
        $this->assertEquals(1000, $finder->getClosedPathValue());
        $this->assertEquals(10, $this->getPropertyValue( $finder, 'm' ));
        $this->assertEquals(100, $this->getPropertyValue( $finder, 'mPercent' ));
        
        $this->assertEquals(1.1, $finder->getAlpha());
        $this->assertEquals(1.1, $finder->getBeta());
        $this->assertEquals(2.1, $finder->getP());
        $this->assertEquals(2.2, $finder->getC());
        $this->assertEquals(110, $finder->getQ());
    }    
    
    public function testMGuard() {
        $finder = $this->getClassicFinder();
        $this->expectException(LogicException::class);
        $finder->setM(-1);
    }
    
    public function testMPercentGuard() {
        $finder = $this->getClassicFinder();
        $this->expectException(LogicException::class);
        $finder->setMPercent(-1);
    }    
    
    public function testAlphaGuard() {
        $finder = $this->getClassicFinder();
        $this->expectException(LogicException::class);
        $finder->setAlpha(-1);
    }   
    
    public function testBetaGuard() {
        $finder = $this->getClassicFinder();
        $this->expectException(LogicException::class);
        $finder->setBeta(-1);
    }     
    
    public function testPGuard() {
        $finder = $this->getClassicFinder();
        $this->expectException(LogicException::class);
        $finder->setP(-1);
    }   
    
    public function testCGuard() {
        $finder = $this->getClassicFinder();
        $this->expectException(LogicException::class);
        $finder->setC(-1);
    }     
    
    public function testQGuard() {
        $finder = $this->getClassicFinder();
        $this->expectException(LogicException::class);
        $finder->setQ(-1);
    }     
    
    public function testInitializeWrong() {
        $finder = $this->getClassicFinder();
        $matrix = $this->getMatrix();
        unset($matrix[1][3]);
        $this->expectException(InvalidArgumentException::class);
        $finder->run($matrix, 1);
    }    
    
    public function testInitializeAntsFromPercent() {
        $finder = $this->getClassicFinder(0);
        $finder->setMPercent(50);
        $matrix = $this->getMatrix();
        $finder->run($matrix, 1);
        $this->assertCount(2, $finder->getAnts());
    }  
    
    public function testBestPathLimit() {
        $finder = $this->getClassicFinder(5);
        $matrix = $this->getMatrix();
        $val = $finder->getClosedPathValue() + 1;
        for ($i = 0; $i < 4; $i++) {
            $matrix[1][$i] = $val;
        }
        $res = $finder->run($matrix, 5);
        $this->assertEmpty($res['path']);
    } 

    public function testNodeChoice0() {
        $finder = $this->getMathematicFinder(0, 20.0); 
        $res = $finder->makeNodeRandomChoice(5, [0,1,2,3,4]);
        
        $this->assertEquals(0, $res);
    }
    
    public function testNodeChoice199() {
        $finder = $this->getMathematicFinder(199, 20.0); 
        $res = $finder->makeNodeRandomChoice(5, [0,1,2,3,4]);
        
        $this->assertEquals(0, $res);
    }    
    
    public function testNodeChoice200() {
        $finder = $this->getMathematicFinder(200, 20.0); 
        $res = $finder->makeNodeRandomChoice(5, [0,1,2,3,4]);
        
        $this->assertEquals(1, $res);
    }        
    
    public function testNodeChoice399() {
        $finder = $this->getMathematicFinder(399, 20.0); 
        $res = $finder->makeNodeRandomChoice(5, [0,1,2,3,4]);
        
        $this->assertEquals(1, $res);
    }    
    
    public function testNodeChoice400() {
        $finder = $this->getMathematicFinder(400, 20.0); 
        $res = $finder->makeNodeRandomChoice(5, [0,1,2,3,4]);
        
        $this->assertEquals(2, $res);
    }     
    
    public function testNodeChoice799() {
        $finder = $this->getMathematicFinder(799, 20.0); 
        $res = $finder->makeNodeRandomChoice(5, [0,1,2,3,4]);
        
        $this->assertEquals(3, $res);
    }     
    
    public function testNodeChoice800() {
        $finder = $this->getMathematicFinder(800, 20.0); 
        $res = $finder->makeNodeRandomChoice(5, [0,1,2,3,4]);
        
        $this->assertEquals(4, $res);
    }    
    
    public function testNodeChoice999() {
        $finder = $this->getMathematicFinder(999, 20.0); 
        $res = $finder->makeNodeRandomChoice(5, [0,1,2,3,4]);
        
        $this->assertEquals(4, $res);
    }     
    
    protected function getClassicFinder($ants = 1) {
        $manager = new Manager();
        $finder = $manager->getFinder();
        if ($ants) {
            $finder->setM($ants);
        }
        return $finder;
    }
    
    protected function getMathematicFinder($randomInt, $pheromonFormula) {
        $finder = $this->getClassicFinder();
        $math = $this->createStub(MathematicsInterface::class);
        $math->method('randomInt')->willReturn($randomInt);
        $math->method('pheromonFormula')->willReturn($pheromonFormula);
        $finder->setMathematics($math);         
        return $finder;
    }
    
    protected function getMatrix() {
        return [
            [ 0 , 263, 184, 335],
            [263,  0 , 287, 157],
            [184, 287,  0 , 259],
            [335, 157, 259,  0]
        ];
    }
}    