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
 * This task solves Shortest path problem
 */
class SppTask extends Task
{
    /**
     * @var int  The node we start from. It must be the inner index of adjacency matrix. See Manager::$matrix
     */
    protected int $from;

    /**
     * @var int  The node we travel to. It must be the inner index of adjacency matrix
     */    
    protected int $to;
    
    /**
     * Constructor 
     * 
     * @param int $from
     * @param int $to
     * @throws \LogicException
     */
    public function __construct(int $from, int $to) {
        if ($from == $to) {
            throw new \LogicException('To and from nodes should be different');
        }
        $this->from = $from;
        $this->to = $to;
    }
       
    /**
     * {@inheritdoc}
     */     
    function initialize(AFinder $finder) : void {
        $this->finder = $finder;
        $this->nodesStart = [$this->from, $this->to];  
        $nodes = $finder->getNodes();
        if (!isset($nodes[$this->from]) || !isset($nodes[$this->to])) {
            throw new \InvalidArgumentException('You set wrong values to "from" or "to" nodes for Shortest path problem.');
        }
    }
    
    /**
     * {@inheritdoc}
     */     
    public function findPath($startNode) : array {
        $path = [$startNode];
        $needReverse = $startNode == $this->to;
        $endNode = $startNode == $this->from ? $this->to : $this->from;
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
            if ($nextNode == $endNode) {
                $process = false;
            } 
            $current = $nextNode;
        }
        if ($needReverse) {
            $path = array_reverse($path);
        }
        return $path;   
    }
    
}




