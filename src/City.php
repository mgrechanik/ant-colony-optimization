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
 * A city.
 * It is used if we want to load data about our graph unto system as a set of points or cities or nodes
 * with their X and Y coordinates.
 * This data will be turned into Adjacency Matrix, see Manager::$matrix for details
 * Distances between cities will be calculated according to choosen strategy.
 */
class City
{
    /**
     * @param float $x  X coordinate 
     * @param float $y  Y coordinate
     * @param string $name  A name we want to keep as an alias since inside all nodes are identified as numbers from 0 to N-1 , where N is their amount.
     */
    public function __construct(public float $x, public float $y, public string $name = '') {
        
    }
}

