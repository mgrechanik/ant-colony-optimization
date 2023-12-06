<?php
declare(strict_types=1); 

namespace mgrechanik\aco\classic;

use mgrechanik\aco\MathematicsInterface;

/**
 * Performing a mathematical part of work
 */
class Mathematics implements MathematicsInterface
{
    /**
     * {@inheritdoc}
     */
    public function pheromonFormula(float $t, float $distance, float $alpha, float $beta) : float {
        return ($t ** $alpha) * ((1 / $distance) ** $beta );
    }    

    /**
     * {@inheritdoc}
     */
    public function evaporationFormula(float $t, float $p) : float {
        return $t * (1 - $p);
    }

    /**
     * {@inheritdoc}
     */
    public function incrementPheromoneAmount(int $q, float $distance) : float {
        return $q / $distance;
    }

    /**
     * {@inheritdoc}
     */
    public function randomInt(int $min, int $max) : int {
        return random_int($min, $max);
    }
    
    /**
     * {@inheritdoc}
     */
    public function arrayShuffle(array $array) : array {
        shuffle($array);
        return $array;
    }    
}
