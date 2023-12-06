<?php
declare(strict_types=1); 

namespace mgrechanik\aco\elitist;

use mgrechanik\aco\classic\Finder as ClassicFinder;

/**
 * ACO Finder who works with Elitist ACO Algorithm.
 * 
 * Some books advice to use 10% of normal ants and 100% of elitist ants.
 * 
 * My tests gave me another combination:
 * 30% of normal ants and 150% of elitist ants and Alpha=0.7 managed to find best known solutions in some Tsplib tasks I tested
 */
class Finder extends ClassicFinder 
{
    /**
     * @var int|null Amount of elitist ants 
     */
    protected ?int $sigma = null;
    
    /**
     * @var int Percent of elitist ants relatively to amount of normal ants
     */
    protected int $sigmaPercent = 50;
    
    /**
     * Setter 
     * 
     * @param int $sigma
     * @return void
     */
    public function setSigma(int $sigma) : void {
        $this->guardPositive($sigma, 'Amount of elitist ants');
        $this->sigma = $sigma;
    } 
    
    /**
     * Setter
     * 
     * @param int $sPercent
     * @return void
     * @throws \LogicException
     */
    public function setSigmaPercent(int $sPercent) : void {
        $this->guardPositive($sPercent, 'Percent of elitist ants');
        $this->sigmaPercent = $sPercent;
    }  
    
    /**
     * {@inheritdoc}
     */
    protected function initialize(array $matrix) : void {
        parent::initialize($matrix);
        if (!$this->sigma) {
            $s = (int) round($this->m * $this->sigmaPercent / 100);
            $this->sigma = $s ?: 1;
        }        
    }
    
    /**
     * {@inheritdoc}
     */    
    protected function runIteration(int $i) : void {
        parent::runIteration($i);
        $this->putElitistAntsPheromone();
    }    
    
    /**
     * Putting additional pheromones from elitist ants
     * 
     * @return void
     */
    protected function putElitistAntsPheromone() : void {
        $bestPath = $this->bestPath['path'];
        $distance = $this->bestPath['distance'];
        if (!empty($bestPath) && ($distance < $this->getClosedPathValue())) {
            $this->putAntPheromone($bestPath, $distance, $this->sigma);
        }
    }
}
