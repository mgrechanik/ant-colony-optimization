<?php
declare(strict_types=1); 

namespace mgrechanik\aco\classic;

/**
 * An Edge between two nodes
 */
class Edge
{
    /**
     * @var float Distance between two nodes
     */
    protected float $distance;

    /**
     * @var float Amount of pheromone on edge 
     */
    protected float $pheromone = 1;
    
    /**
     * Constructor 
     * 
     * @param float $distance
     * @param float $pheromone
     */
    public function __construct(float $distance, float $pheromone) {
        $this->distance = $distance;
        $this->pheromone = $pheromone;
    }
    
    /**
     * Getter
     * 
     * @return float
     */
    public function getPheromone() : float {
        return $this->pheromone;
    }
    
    /**
     * Setter
     * 
     * @param float $pheromone
     * @return void
     */
    public function setPheromone(float $pheromone) : void {
        $this->pheromone = $pheromone;
    }    
    
    /**
     * Getter
     * 
     * @return float
     */    
    public function getDistance() : float {
        return $this->distance;
    }    
}

