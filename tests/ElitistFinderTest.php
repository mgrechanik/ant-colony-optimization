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
use mgrechanik\aco\elitist\Finder;
use Yoast\PHPUnitPolyfills\Helpers\AssertAttributeHelper;
use LogicException;

class ElitistFinderTest extends \PHPUnit\Framework\TestCase
{
    use AssertAttributeHelper;
 
    public function testSetters() {
        $finder = $this->getElitistFinder();
        $finder->setSigma(5);
        $finder->setSigmaPercent(90);
        
        $this->assertEquals(5, $this->getPropertyValue( $finder, 'sigma' ));
        $this->assertEquals(90, $this->getPropertyValue( $finder, 'sigmaPercent' ));
    }
    
    public function testSetSigmaWrong() {
        $finder = $this->getElitistFinder();
        $this->expectException(LogicException::class);
        $finder->setSigma(-5);
    }    
    
    public function testSetSigmaPercentWrong() {
        $finder = $this->getElitistFinder();
        $this->expectException(LogicException::class);
        $finder->setSigmaPercent(-5);
    }     
    
    public function testInitialize() {
        $finder = $this->getElitistFinder(2);
        $finder->setSigmaPercent(150);
        $finder->run($this->getMatrix(), 1);
        
        $this->assertEquals(3, $this->getPropertyValue( $finder, 'sigma' ));
    }
    
    protected function getElitistFinder($ants = 1) {
        $finder = new Finder();
        $manager = new Manager(finder : $finder);
        //$finder = $manager->getFinder();
        if ($ants) {
            $finder->setM($ants);
        }
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
