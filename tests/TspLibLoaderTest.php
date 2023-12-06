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

use mgrechanik\aco\TspLibLoader;
use mgrechanik\aco\Manager;

class TspLibLoaderTest extends \PHPUnit\Framework\TestCase
{
    private $fixtureDir;
    
    public function setUp() : void {
        parent::setUp();
        $this->fixtureDir = __DIR__ . '/fixtures/tsplib/';
    }
    
    /**
     * @dataProvider wrongEuc2dFormatsProvider
     */     
    public function testTypeMissedInEuc2d($file) : void {
        $this->expectException(\Exception::class);
        $file = $this->fixtureDir . $file;
        TspLibLoader::loadCitiesFromEuc2dFile($file);
    }
    
    public function testMissedNordEuc2d() : void {
        $file = $this->fixtureDir . '/wrong_format/wrong_missed_nord.tsp';
        $cities = TspLibLoader::loadCitiesFromEuc2dFile($file);
        $this->assertEmpty($cities);
    }  
    
    public function testNormEuc2d() : void {
        $file = $this->fixtureDir . '/berlin52.tsp';
        $cities = TspLibLoader::loadCitiesFromEuc2dFile($file);
        $this->assertCount(52, $cities);
        
        $this->assertSame('1', $cities[0]->name);
        $this->assertSame(565.0, $cities[0]->x);
        $this->assertSame(575.0, $cities[0]->y);

        $this->assertSame('52', $cities[51]->name);
    }  
    
    /**
     * @dataProvider wrongMatrixFormatsProvider
     */    
    public function testMatrixWrongFormats(string $file)  : void {
        $this->expectException(\Exception::class);
        TspLibLoader::loadMatrixFromExplicitMatrixFile($this->fixtureDir . $file);        
    }
    
    public function testMatrixNoEdgeEuc2d() : void {
        $file = $this->fixtureDir . '/wrong_format/wrong_matrix_no_edge.tsp';
        $matrix = TspLibLoader::loadMatrixFromExplicitMatrixFile($file);
        $this->assertEmpty($matrix);
    }   
    
    public function testNormMatrix() : void {
        $file = $this->fixtureDir . '/bays29.tsp';
        $matrix = TspLibLoader::loadMatrixFromExplicitMatrixFile($file);
        $this->assertCount(29, $matrix);
        
        $this->assertSame(0.0, $matrix[0][0]);
        $this->assertSame(167.0, $matrix[0][28]);
        $this->assertSame(167.0, $matrix[28][0]);
        $this->assertSame(0.0, $matrix[28][28]);
    }  
    
    /**
     * Testing correct calculation of the best path the library gives
     * Results are seen at: http://comopt.ifi.uni-heidelberg.de/software/TSPLIB95/STSP.html
     * Best path are located in library's <name>.opt.tour.gz
     */
    public function testBerlin52Result() {
        $file = $this->fixtureDir . '/berlin52.tsp';
        $cities = TspLibLoader::loadCitiesFromEuc2dFile($file);    
        $manager = new Manager();
        $manager->setCities(...$cities);
        $this->assertEquals(7542, $manager->countPath([
            1,49,32,45,19,41,8,9,10,43,33,51,11,52,14,13,47,26,27,28,12,25,4,6,15,5,24,48,38,37,40,
            39,36,35,34,44,46,16,29,50,20,23,30,2,7,42,21,17,3,18,31,22,1], true));
    }
    
    /**
     * See self::testBerlin52Result
     */
    public function testBays29Result() {
        $file = $this->fixtureDir . '/bays29.tsp';
        $matrix = TspLibLoader::loadMatrixFromExplicitMatrixFile($file);  
        $manager = new Manager();
        // tsplib95 library names nodes starting with "1"
        $manager->setMatrix($matrix, 1);
        $this->assertEquals(2020, $manager->countPath([
            1,28,6,12,9,5,26,29,3,2,20,10,4,15,18,17,14,22,11,19,25,7,23,27,8,24,16,13,21,1], true));
    }    

    public static function wrongMatrixFormatsProvider() : array {
        return [
            ['/wrong_format/wrong_matrix_no_weight.tsp'],
            ['/wrong_format/wrong_matrix_wrong_weight.tsp'],
            ['/wrong_format/wrong_matrix_no_format.tsp'],
            ['/wrong_format/wrong_matrix_wrong_format.tsp'], 
            ['/wrong_format/wrong_missed_type.tsp'],
            ['/wrong_format/wrong_type.tsp'],
        ];
    }
    
    public static function wrongEuc2dFormatsProvider() : array {
        return [
            ['/wrong_format/wrong_missed_type.tsp'],
            ['/wrong_format/wrong_type.tsp'],
            ['/wrong_format/wrong_missed_edge.tsp'],
            ['/wrong_format/wrong_wrong_edge.tsp'], 
        ];
    }    
}