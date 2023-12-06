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
 * Interface to calculate distance beetween two points , who are presented with their X and Y coordinates
 */
interface DistanceInterface
{
    /**
     * Returns the distance between two points(or cities)
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @return float
     */
    public function distance(float $x1, float $y1, float $x2, float $y2) : float;
}

