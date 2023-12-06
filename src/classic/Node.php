<?php
declare(strict_types=1); 

namespace mgrechanik\aco\classic;

use mgrechanik\aco\AFinder;

/**
 * A node of our graph.
 * 
 * It is not very functional now, since classic ACO method does not need this.
 * But it's descendants may need to hold some interesting data for different ACO variations.
 * Say, make preferences to not visited directions.  
 */
class Node
{
    /**
     * @var int  Number of the node 
     */
    protected int $number;
    
    /**
     * @var Afinder Finder 
     */
    protected AFinder $finder;

    /**
     * Constructor 
     * 
     * @param int $number
     * @param AFinder $finder
     */
    public function __construct(int $number,  AFinder $finder ) {
        $this->number = $number;
        $this->finder = $finder;
    }
    
    /**
     * Getter
     * 
     * @return int
     */
    public function getNumber() : int {
        return $this->number;
    }
}
