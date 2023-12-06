<?php
declare(strict_types=1); 

namespace mgrechanik\aco\classic;

use mgrechanik\aco\AFinder;
use Exception;

/**
 * Classic ACO Finder.
 * 
 * This finder implements classic ACO algorithm.
 * It works like described in the wiki and articles/works about ACO.
 */
class Finder extends AFinder
{
    /**
     * {@inheritdoc}
     */
    public function run(array $matrix, int $iterations) : array {
        if ($iterations < 1) {
            throw new Exception('There should be some iterations');
        }        
        if (empty($this->task) || empty($this->mathematics)) {
            throw new Exception('Task or Mathematiks need to be configured');
        }
        if (count($matrix) < 2) {
            throw new Exception('Matrix is incorrect');
        }
        $this->initialize($matrix);
        $this->task->initialize($this);
        $this->iterations = $iterations;
        $this->prepareAnts();
        for ($i = 0; $i < $this->iterations; $i++) {
            $this->runIteration($i);
        }
        return $this->bestPath;
    }
}
