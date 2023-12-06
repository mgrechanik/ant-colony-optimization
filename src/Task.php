<?php
/**
 * This file is part of the mgrechanik/ant-colony-optimization library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/ant-colony-optimization/blob/main/LICENCE.md
 * @link https://github.com/mgrechanik/ant-colony-optimization
 */

declare(strict_types=1); 

namespace mgrechanik\aco;

/**
 * Task we are solving
 * 
 * For example we can be solving 
 *   - Travelling salesman problem  (1)
 *   - Shortest path problem.       (2)
 *   - Constrained Shortest Path First
 *    
 *     (1) and (2) are implemented in the library
 * 
 * You can create the task you need to solve with ACO on adjacency matrix
 */
abstract class Task
{
    /**
     * @var AFinder Finder 
     */
    protected AFinder $finder;

    /**
     * @var array Nodes with which our ants are starting their tours 
     */
    protected array $nodesStart = [];
    
    /**
     * Initialization
     * 
     * @param AFinder $finder
     */
    abstract public function initialize(AFinder $finder) : void;
    
    /**
     * Finding a path starting from concrete node
     * 
     * @param int $startNode Node we start a tour of an ant
     * @return array
     */
    abstract public function findPath(int $startNode) : array;
    
    /**
     * Returns an array of nodes all ants could start from
     * 
     * @param int $antCount
     * @return array
     */
    public function getNodesStartRange(int $antCount) : array {
        $count = count($this->nodesStart);
        $add = $range = $this->nodesStart;
        // in case we have more ants than nodes
        while ($antCount > $count) {
            $range = array_merge($range, $add);
            $count = count($range);
        }
        return $range;
    }
    
    /**
     * Ant makes a next move
     * 
     * @param int $current
     * @param array $choices
     * @throws \Exception
     * @return int Choice
     */
    protected function makeMove(int $current, array $choices) : int {
        $choices = array_values($choices);
        $res = $this->finder->makeNodeRandomChoice($current, $choices);
        return $choices[$res];
    }    

}