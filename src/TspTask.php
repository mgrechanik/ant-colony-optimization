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
 * This task solves Travelling salesman problem.
 * 
 * Ant should start from node A, visit all nodes and return to A.
 * 
 * It solves both Asymmetric and Symmetric type of TSP
 */
class TspTask extends Task
{
    /**
     * {@inheritdoc}
     */    
    function initialize(AFinder $finder) : void {
        $this->finder = $finder;
        $count = count($finder->getNodes());
        $this->nodesStart = range(0, $count - 1);        
    }
    
    /**
     * {@inheritdoc}
     */    
    public function findPath($startNode) : array {
        $path = [$startNode];
        $finder = $this->finder;
        $nodes = $finder->getNodes();
        $possibleNodes = array_values(array_diff(array_keys($nodes), $path));
        $current = $startNode;
        $nextNode = null;
        $process = true;
        while($process) {
            $count = count($possibleNodes);
            if ($count == 1) {
                $process = false;
                $nextNode = $possibleNodes[0];
            } else {
                $nextNode = $this->makeMove($current, $possibleNodes);
                $possibleNodes = array_values(array_diff($possibleNodes, [$nextNode]));
            }
            $path[] = $nextNode;
            $current = $nextNode;
        }       
        return $this->normalizePath($path);        
    }
    
    /**
     * We normalize the path so there are no dublicates of the same path
     * 
     * Path starts from node 0 to the node with smaller number and returns to 0.
     * Example - [0, 1, 3, 2, 0]
     *  
     * @param array $path
     * @return array
     */
    public function normalizePath(array $path) : array {
        $arr1 = $arr2 = [];
        $found = false;
        foreach ($path as $val) {
            if ($val === 0) {
                $found = true;
            }
            if ($found) {
                $arr1[] = $val;
            } else {
                $arr2[] = $val;
            }
        }
        $res = array_values(array_merge($arr1, $arr2, [0]));
        $count = count($res);
        if ($res[1] > $res[$count - 2]) {
            $res = array_reverse($res);
        }
        return $res;
    }    
}
