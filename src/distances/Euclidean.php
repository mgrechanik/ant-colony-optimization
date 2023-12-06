<?php
declare(strict_types=1); 

namespace mgrechanik\aco\distances;
use mgrechanik\aco\DistanceInterface;

/**
 * Euclidean distance
 */
class Euclidean implements DistanceInterface
{
    /**
     * {@inheritdoc}
     */    
    public function distance(float $x1, float $y1, float $x2, float $y2) : float {
        return round(sqrt( ($x1 - $x2) ** 2 + ($y1 - $y2) ** 2 ));
    }
}
