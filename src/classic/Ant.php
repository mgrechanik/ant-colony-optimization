<?php
declare(strict_types=1); 

namespace mgrechanik\aco\classic;

use mgrechanik\aco\AFinder;

/**
 * An ant who travels
 */
class Ant
{
    /**
     * @var int Ant's number 
     */
    protected int $number;

    /**
     * @var int The node from which the ant starts it's tour on new iteration 
     */
    protected int $startNode = 0;
    
    /**
     * @var AFinder  Finder 
     */
    protected AFinder $finder;
    
    /**
     * @var float Distance of the path found
     */
    protected float $distance = 0;
    
    /**
     * @var array Path ant found 
     */
    protected array $path = [];
    
    /**
     * Constructor
     * 
     * @param int $number
     * @param AFinder $finder
     */
    public function __construct(int $number, AFinder $finder) {
        $this->number = $number;
        $this->finder = $finder;
    }  
    
    /**
     * Getter
     * 
     * @return array
     */
    public function getPath() : array {
        return $this->path;
    }
    
    /**
     * Getter
     * 
     * @return float
     */
    public function getDistance() : float {
        return $this->distance;
    }    

    /**
     * Getter
     * 
     * @return int
     */
    public function getNumber() : int {
        return $this->number;
    }
    
    /**
     * Put the ant to a new start of finding a path
     * 
     * @param int $startNode
     * @return void
     */
    public function newStart(int $startNode) : void {
        $this->startNode = $startNode;
        $this->path = [$startNode];
        $this->distance = 0;
        
    }
    
    /**
     * Find a path
     * 
     * @return void
     * @throws \LogicException
     */
    public function findPath() : void {
        if (count($this->path) !== 1) {
            throw new \LogicException('You must start searching a path only after new start');
        }
        $this->path = $this->finder->getTask()->findPath($this->startNode);
        $this->distance = $this->finder->countDistance($this->path);
    }
        
}
