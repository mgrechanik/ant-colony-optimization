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

use mgrechanik\aco\Manager;
use Yoast\PHPUnitPolyfills\Helpers\AssertAttributeHelper;
use mgrechanik\aco\AFinder;
use mgrechanik\aco\MathematicsInterface;
use mgrechanik\aco\DistanceInterface;
use mgrechanik\aco\Task;
use mgrechanik\aco\City;
use LogicException;
use InvalidArgumentException;

class ManagerTest extends \PHPUnit\Framework\TestCase
{
    use AssertAttributeHelper;
    
    public function testCreating() {
        $manager = new Manager();
        $finder = $manager->getFinder();
        $this->assertInstanceOf(AFinder::class, $finder);
        $this->assertInstanceOf(MathematicsInterface::class, $this->getPropertyValue( $manager, 'mathematics' ));
        $this->assertInstanceOf(DistanceInterface::class, $this->getPropertyValue( $manager, 'distanceStrategy' ));
        $this->assertInstanceOf(Task::class, $this->getPropertyValue( $manager, 'task' ));
        
        $this->assertNotNull($finder->getTask());
        $this->assertNotNull($finder->getMathematics());
        $this->assertEmpty($manager->getInnerPath());
    }
    
    public function testGetNamedFromIndexedPath() {
        $cities = [new City(1,1, 'K'), new City(1,1, 'S')];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $this->assertSame(['K', 'S'], $manager->getNamedFromIndexedPath([0, 1]));
    }
    
    public function testGetNamedFromIndexedPathWrong() {
        $cities = [new City(1,1, 'K'), new City(1,1, 'S')];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $this->expectException(LogicException::class);
        $manager->getNamedFromIndexedPath([0, 2]);
    }    
    
    public function testGetIndexedFromNamedPathh() {
        $cities = [new City(1,1, 'K'), new City(1,1, 'S')];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $this->assertSame([0, 1],$manager->getIndexedFromNamedPath(['K', 'S']));
    }
    
    public function testGetIndexedFromNamedPathWrong() {
        $cities = [new City(1,1, 'K'), new City(1,1, 'S')];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $this->expectException(LogicException::class);
        $manager->getIndexedFromNamedPath(['L']);
    }     
    
    public function testSetCitiesKeys() {
        $cities = [10 => new City(1,1, 'K'), new City(1,1, 'S')];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $cities = $manager->getCities();
        $this->assertSame([0, 1], array_keys($cities));
        $this->assertSame(['K' => 0, 'S' => 1], $this->getPropertyValue( $manager, 'nameIndex' ));
    }
    
    public function testSetCitiesNoNames() {
        $cities = [new City(1,1), new City(1,1)];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $this->assertSame(['0' => 0, '1' => 1], $this->getPropertyValue( $manager, 'nameIndex' ));
    }    
    
    public function testSetCitiesWrongNames() {
        $cities = [new City(1,1, 'K'), new City(1,1, 'K')];
        $manager = new Manager();
        $this->expectException(LogicException::class);
        $manager->setCities(...$cities);
    }  
    
    public function testBuildMatrixFromCities() {
        $cities = [new City(1,1), new City(6,13)];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $matrix = $manager->getMatrix();
        $this->assertArrayHasKey(0, $matrix);
        $this->assertArrayHasKey(1, $matrix);

        $this->assertArrayHasKey(0, $matrix[0]);
        $this->assertArrayHasKey(1, $matrix[0]);        
        
        $this->assertArrayHasKey(0, $matrix[1]);
        $this->assertArrayHasKey(1, $matrix[1]);  
        
        $this->assertEquals(13, $matrix[0][1]);
        $this->assertEquals(13, $matrix[1][0]);
        
        $this->assertEquals(0, $matrix[0][0]);
        $this->assertEquals(0, $matrix[1][1]);
    }
    
    public function testSetMatrixNotEmpty() {
        $cities = [new City(1,1), new City(6,13)];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $this->expectException(LogicException::class);
        $manager->setMatrix([]);
    }
    
    public function testSetMatrixWrongSize() {
        $manager = new Manager();
        $this->expectException(InvalidArgumentException::class);
        $matrix = [[1, 2], [1, 2], [1, 2]];
        $manager->setMatrix($matrix);
    }    
    
    public function testSetMatrixWrongType() {
        $manager = new Manager();
        $this->expectException(InvalidArgumentException::class);
        $matrix = [[1, []], [1, 2]];
        $manager->setMatrix($matrix);
    }  

    public function testSetMatrixWrongNegative() {
        $manager = new Manager();
        $this->expectException(InvalidArgumentException::class);
        $matrix = [[0, 0], [1, 2]];
        $manager->setMatrix($matrix);
    } 
    
    public function testSetMatrixCorrect() {
        $manager = new Manager();
        $matrixStart = [[0, 1], 
                        [1, 0]];
        $manager->setMatrix($matrixStart);
        $matrix = $manager->getMatrix();
        $this->assertEquals($matrixStart, $matrix);
        $this->assertSame(['0' => 0, '1' => 1], $this->getPropertyValue( $manager, 'nameIndex' ));
        $this->assertSame([0, 1], array_keys($manager->getCities()));
    }    
    
    public function testSetMatrixNameStart() {
        $manager = new Manager();
        $matrixStart = [[0, 1], 
                        [1, 0]];
        $manager->setMatrix($matrixStart, 5);
        $this->assertSame(['5' => 0, '6' => 1], $this->getPropertyValue( $manager, 'nameIndex' ));
    } 
    
    public function testUpdateMatrixWrongKeysX() {
        $manager = new Manager();
        $this->expectException(InvalidArgumentException::class);
        $matrix = [[0, 1], [1, 0]];
        $manager->setMatrix($matrix);
        $manager->updateMatrix(0, 2, 4);
    }    
    
    public function testUpdateMatrixWrongKeysY() {
        $manager = new Manager();
        $this->expectException(InvalidArgumentException::class);
        $matrix = [[0, 1], [1, 0]];
        $manager->setMatrix($matrix);
        $manager->updateMatrix(2, 0, 4);
    }     
    
    public function testUpdateMatrixWrongValue() {
        $manager = new Manager();
        $this->expectException(InvalidArgumentException::class);
        $matrix = [[0, 1], [1, 0]];
        $manager->setMatrix($matrix);
        $manager->updateMatrix(0, 1, 0);
    }      
    
    public function testUpdateMatrixCorrect() {
        $manager = new Manager();
        $matrix = [[0, 1], [1, 0]];
        $manager->setMatrix($matrix);
        $manager->updateMatrix(0, 1, 5);
        $matrix = $manager->getMatrix();
        $this->assertEquals(5, $matrix[0][1]);
        $this->assertEquals(5, $matrix[1][0]);
    }     
    
    public function testUpdateMatrixCorrectNoDouble() {
        $manager = new Manager();
        $matrix = [[0, 1], [1, 0]];
        $manager->setMatrix($matrix);
        $manager->updateMatrix(0, 1, 5, false);
        $matrix = $manager->getMatrix();
        $this->assertEquals(5, $matrix[0][1]);
        $this->assertEquals(1, $matrix[1][0]);
    }  
    
    public function testCountPathWrongPath() {
        $manager = new Manager();
        $this->expectException(LogicException::class);
        $matrix = [[0, 1], [1, 0]];
        $manager->setMatrix($matrix);
        $manager->countPath([1]);
    }     
    
    public function testCountPathNoMatrixh() {
        $manager = new Manager();
        $this->expectException(LogicException::class);
        $manager->countPath([1, 2]);
    }  
    
    public function testCountPathWrongElements() {
        $manager = new Manager();
        $this->expectException(LogicException::class);
        $matrix = [[0, 1], [1, 0]];
        $manager->setMatrix($matrix);
        $manager->countPath([0, 2]);
    }        
    
    public function testCountPathCorrect() {
        $manager = new Manager();
        $matrix = [[0, 9], [9, 0]];
        $manager->setMatrix($matrix);
        $this->assertEquals(27, $manager->countPath([0, 1, 0, 1]));
        $this->assertEquals(0, $manager->countPath([0, 0]));
    }      
    
    public function testCountPathCorrectIndexed() {
        $manager = new Manager();
        $matrix = [[0, 9], [9, 0]];
        $manager->setMatrix($matrix, 100);
        $this->assertEquals(27, $manager->countPath(['100', '101', '100', '101'], true));
        $this->assertEquals(27, $manager->countPath([100, 101, 100, 101], true));
    }     
    
    public function testRunEmptyMatrix() {
        $manager = new Manager();
        $this->expectException(LogicException::class);
        $manager->run(1);
    }     
    
    public function testRunFinderResult() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('run')->willReturn('wrong');
        $manager = new Manager(finder : $finder);
        $matrix = [[0, 9], [9, 0]];
        $manager->setMatrix($matrix);

        $this->assertNull($manager->run(1));
    }    
    
    public function testRunFinderNotFound() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('run')->willReturn([
            'path' => [],
            'distance' => 1000
        ]);
        $manager = new Manager(finder : $finder);
        $matrix = [[0, 9], [9, 0]];
        $manager->setMatrix($matrix);
        $manager->run(1);
        
        $this->assertNull($manager->getDistance());
        $this->assertSame([], $manager->getInnerPath());
    }  
    
    public function testRunFinderFound() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('run')->willReturn([
            'path' => [0, 1],
            'distance' => 9
        ]);
        $manager = new Manager(finder : $finder);
        $matrix = [[0, 9], [9, 0]];
        $manager->setMatrix($matrix, 10);
        $distance = $manager->run(1);
        
        $this->assertEquals(9, $distance);
        $this->assertEquals(9, $manager->getDistance());
        $this->assertSame([0, 1], $manager->getInnerPath());
        $this->assertEquals([10, 11], $manager->getNamedPath());
    }    
}