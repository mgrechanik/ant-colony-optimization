<?php
/**
 * This file is part of the mgrechanik/ant-colony-optimization library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/ant-colony-optimization/blob/main/LICENSE.md
 * @link https://github.com/mgrechanik/ant-colony-optimization
 */
declare(strict_types=1); 

namespace mgrechanik\aco;

/**
 * Interface to do a mathematical part of work
 */
interface MathematicsInterface
{
    /**
     * Formula with which we calculate possibility of ant to choose this direction
     * 
     * @param float $t Amount of pheromone
     * @param float $distance Distance between nodes
     * @param float $alpha Alpha Coefficient
     * @param float $beta Beta Coefficient
     * @return float
     */
    public function pheromonFormula(float $t, float $distance, float $alpha, float $beta) : float;
    
    /**
     * Formula of evaporation of pheromones
     * 
     * @param float $t Amount of pheromone which exists on edge
     * @param float $p P Coefficient
     * @return float
     */
    public function evaporationFormula(float $t, float $p) : float;
    
    /**
     * Formula to calculate amount of pheromone we add to path
     * 
     * @param int $q Q Coefficient
     * @param float $distance
     * @return float
     */
    public function incrementPheromoneAmount(int $q, float $distance) : float;
    
    /**
     * Calculate random int 
     * 
     * @param int $min
     * @param int $max
     * @return int
     */
    public function randomInt(int $min, int $max) : int;
    
    /**
     * Shuffles an array 
     * 
     * @param array $array
     * @return array
     */
    public function arrayShuffle(array $array) : array;    
}