<?php
/**
 * This file is part of the mgrechanik/ant-colony-optimization library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/ant-colony-optimization/blob/main/LICENSE.md
 * @link https://github.com/mgrechanik/ant-colony-optimization
 */
declare(strict_types=1); 

namespace mgrechanik\aco;

use mgrechanik\aco\classic\Ant;
use mgrechanik\aco\classic\Edge;
use mgrechanik\aco\classic\Node;

/**
 * Basic ACO Finder.
 * 
 */
abstract class AFinder
{
    /**
     * @var int Amount of iterations to work 
     */
    protected int $iterations = 400;
    
    /**
     * @var int  The value of the path distance which is treated as the limit.
     * Paths with distance bigger than this are not considered valid.
     * You may use it to close some edges, make them impassable
     */
    protected int $closed_path_value =  1000000;
    
    /**
     * @var int  Number of ants
     * You can set it explicitly or use percent from nodes, See self::$mPercent
     */
    protected ?int $m = null;

    /**
     * @var int Percent of ants relatively to amount of nodes (it is used when self::$m is not set explicitly)
     */
    protected int $mPercent = 40;
    
    /**
     * @var float  Alpha coefficient
     */
    protected float $alpha = 1;

    /**
     * @var float  Beta coefficient
     */
    protected float $beta = 5;

    /**
     * @var float  Evaporation coefficient
     * Basically it should be a number from 0 to 1, when follow classic formula ($t * (1 - $p);).
     * But I do not put this restriction since library allows to experiment with your own formula.
     */
    protected float $p = 0.1;
    
    /**
     * @var float  Starting pheromone value coefficient
     * That is what we put on edges before the work starts
     */
    protected float $c = 1;    

    /**
     * @var int  Q coefficient
     */
    protected int $q = 100;    
    
    /**
     * @var array Edges of the matrix 
     */
    protected array $edges = [];
    
    /**
     * @var Node[] Nodes
     */
    protected array $nodes = [];
    
    /**
     * @var Ant[] Ants who are going to travel
     */
    protected array $ants = [];
    
    /**
     * @var array Keep the one best path found 
     */
    protected array $bestPath = [
        'distance' => null,
        'key' => '',
        'path' => [],
        'time_spent' => 0
    ];
    
    /**
     * @var array History of finding best paths 
     */
    protected array $history = [];

    /**
     * @var Manager 
     */
    protected Manager $manager;

    /**
     * @var MathematicsInterface  Strategy to do mathematic work
     */
    protected MathematicsInterface $mathematics;
    
    /**
     * @var Task   Task we are solving
     */
    protected Task $task;
    
    /**
     * @var float Timestart of our search 
     */
    protected float $timeStart = 0;
    
    /**
     * Setter
     * 
     * @param int $iterations
     * @return void
     */  
    public function setIterations(int $iterations) : void {
        $this->iterations = $iterations;
    }
    
    /**
     * Setter 
     * 
     * @param int $value
     * @return void
     */  
    public function setClosedPathValue(int $value) : void {
        $this->closed_path_value = $value;
    }    
    
    /**
     * Setter
     * 
     * @param int $m
     * @return void
     */  
    public function setM(int $m) : void {
        $this->guardPositive($m, 'Amount of ants');
        $this->m = $m;
    }    

    /**
     * Setter 
     * 
     * @param int $mPercent
     * @return void
     * @throws \LogicException
     */  
    public function setMPercent(int $mPercent) : void {
        $this->guardPositive($mPercent, 'Percent of ants');
        $this->mPercent = $mPercent;
    } 
    
    /**
     * Setter
     * 
     * @param float $alpha
     * @return void
     */ 
    public function setAlpha(float $alpha) : void {
        $this->guardPositive($alpha, 'Alpha');
        $this->alpha = $alpha;
    }

    /**
     * Setter
     * 
     * @param float $beta
     * @return void
     */  
    public function setBeta(float $beta) : void {
        $this->guardPositive($beta, 'Beta');
        $this->beta = $beta;
    }

    /**
     * Setter
     * 
     * @param float $p
     * @return void
     */ 
    public function setP(float $p) : void {
        $this->guardPositive($p, 'Evaporation coefficient');
        $this->p = $p;
    }
    
    /**
     * Setter
     * 
     * @param float $c
     * @return void
     */  
    public function setC(float $c) : void {
        $this->guardPositive($c, 'Starting amount of pheromone');
        $this->c = $c;
    }    

    /**
     * Setter
     * 
     * @param int $q
     * @return void
     */  
    public function setQ(int $q) : void {
        $this->guardPositive($q, 'Q coefficient');
        $this->q = $q;
    } 

    /**
     * Setter
     * 
     * @param Manager $manager
     * @return void
     */  
    public function setManager(Manager $manager) : void {
        $this->manager = $manager;
    }
    
    /**
     * Setter
     * 
     * @param MathematicsInterface $mathematics
     */ 
    public function setMathematics(MathematicsInterface $mathematics) {
        $this->mathematics = $mathematics;
    }
    
    /**
     * Setter
     * 
     * @param Task $task
     * @return void
     */    
    public function setTask(Task $task) : void {
        $this->task = $task;
    }     
    
    /**
     * Getter
     * 
     * @return int
     */   
    public function getClosedPathValue() : int {
        return $this->closed_path_value;
    }
    
    /**
     * Getter
     * 
     * @return float
     */ 
    public function getAlpha() : float {
        return $this->alpha;
    }

    /**
     * Getter
     * 
     * @return float
     */   
    public function getBeta() : float {
        return $this->beta;
    }

    /**
     * Getter
     * 
     * @return float
     */  
    public function getP() : float {
        return $this->p;
    }
    
    /**
     * Getter
     * 
     * @return float
     */  
    public function getC() : float {
        return $this->c;
    }    
    
    /**
     * Getter
     * 
     * @return int
     */ 
    public function getQ() : int {
        return $this->q;
    }  
    
    /**
     * Getter
     * 
     * @return array
     */ 
    public function getHistory(): array {
        return $this->history;
    }

    /**
     * Getter
     * 
     * @return float
     */ 
    public function getTimeStart(): float {
        return $this->timeStart;
    }

    /**
     * Returns string representation of time which passed from start of work. 
     * In seconds 
     * @return string
     */ 
    public function getTimeFromStart(): string {
        return number_format(microtime(true) - $this->getTimeStart(), 2);
    }       

    /**
     * Getter
     * 
     * @return Task
     */  
    public function getTask() : Task {
        return $this->task;
    }     
    
    /**
     * Getter
     * 
     * @return Manager
     */  
    public function getManager() : Manager {
        return $this->manager;
    }     
    
    /**
     * Returns all nodes
     * 
     * @return array
     */  
    public function getNodes() : array {
        return $this->nodes;
    }
    
    /**
     * Returns all egdes
     * 
     * @return array
     */ 
    public function getEdges() : array {
        return $this->edges;
    }   
    
    /**
     * Returns all ants
     * 
     * @return array
     */ 
    public function getAnts() : array {
        return $this->ants;
    }    
    
    /**
     * Getter
     * 
     * @return MathematicsInterface
     */ 
    public function getMathematics() : MathematicsInterface {
        return $this->mathematics;
    }
    
    /**
     * Returns amount of pheromone on edge between two nodes
     * 
     * @param int $i
     * @param int $j
     * @return float
     */ 
    public function getPheromoneOnEdge(int $i, int $j) : float {
        if (isset($this->edges[$i][$j])) {
            $edge = $this->edges[$i][$j];
            return $edge->getPheromone();
        }
        return 0;
    }
    
    /**
     * Returns distance between two nodes
     * 
     * @param int $i
     * @param int $j
     * @return float
     * @throws \Exception
     */ 
    public function getDistanceOnEdge(int $i, int $j) : float {
        if (isset($this->edges[$i][$j])) {
            $edge = $this->edges[$i][$j];
            $distance = $edge->getDistance();
            if (!$distance) {
                throw new \Exception('Distance between nodes cannot be zero');
            }
            return $distance;
        }
        return $this->getClosedPathValue();
    }    
    
    /**
     * Runs simulation to find the best solution of the task
     * 
     * @param array $matrix Adjacency Matrix
     * @param int $iterations Amount of iterations to work through
     * @return array Best path
     * @throws \Exception
     */   
    abstract public function run(array $matrix, int $iterations);
    
    /**
     * Returns the choice of direction for ant to move
     * 
     * @param int $current The node ant at
     * @param array $choices The possible choices to move
     * @return int The number on node to travel
     */ 
    public function makeNodeRandomChoice(int $current, array $choices) : int {
        $count = count($choices);
        $values = [];
        for ($i = 0; $i < $count; $i++) {
            $values[$i] = $this->mathematics->pheromonFormula(
                $this->getPheromoneOnEdge($current, $choices[$i]),
                $this->getDistanceOnEdge($current, $choices[$i]),
                $this->getAlpha(),
                $this->getBeta(),
            );
        }
        $sum = array_sum($values);
        $percents = [];
        for ($i = 0; $i < $count; $i++) {
            $percents[$i] = $values[$i] * 1000 / $sum;
        }
        $rand = $this->mathematics->randomInt(0, 999);
        $border = 0;
        $res = null;
        for ($i = 0; $i < $count; $i++) {
            $border = $border + $percents[$i];
            if ($rand < $border) {
                $res = $i;
                break;
            }
        }
        $res ??= 0;
        return $res;
    }     
    
    /**
     * Calculating the distance of all path
     * 
     * @param array $path
     * @return float
     */ 
    public function countDistance(array $path) : float {
        $sum = 0;
        $count = count($path);
        for ($i = 0; $i < $count - 1 ; $i++) {
            $ind1 = $path[$i];
            $ind2 = $path[$i + 1];
            if (isset($this->edges[$ind1][$ind2] )) {
                $edge = $this->edges[$ind1][$ind2];
                $sum += $edge->getDistance();				
            } 
        }
        return $sum;
    }    
    
    /**
     * Initialization of the simulation process
     * 
     * @param array $matrix
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function initialize(array $matrix) : void {
        $count = count($matrix);
        for ($i = 0; $i < $count; $i++) {
            $this->edges[$i] = [];
            for ($j = 0; $j < $count; $j++) {
                if (!isset($matrix[$i][$j])) {
                    throw new \InvalidArgumentException('Matrix format is wrong');
                }
                $this->edges[$i][$j] = $this->createEdge($matrix[$i][$j], $this->c);
            } 
            $this->nodes[$i] = $this->createNode($i);
        } 
        if (!$this->m) {
            $m = (int) round($count * $this->mPercent / 100);
            $this->m = $m ?: 1;
        }
        $this->history = [];
        $this->timeStart = microtime(true);
        $this->bestPath['key'] = '';
        $this->bestPath['path'] = [];
        $this->bestPath['distance'] = $this->closed_path_value;
    }    
    
    /**
     * Runs one iteration.
     * 
     * During this iteration 
     *   - all ants build their solutions
     *   - evaporation of existing pheromons happens
     *   - best path is checked
     *   - Ants put pheromones on paths they found
     * 
     * @param int $i Number of iteration
     * @return void
     */
    protected function runIteration(int $i) : void {
        $this->makeAntsMove();
        $this->performEvaporation();
        foreach ($this->ants as $ant) {
            $this->checkBestPath($ant->getPath(), $ant->getDistance(), $i);
            $this->putAntPheromone($ant->getPath(), $ant->getDistance());
        }
    }
    
    /**
     * All ants find their path solutions
     * 
     * @return void
     */
    protected function makeAntsMove() : void {
        // Find The cities range from which the ants will start their tours
        $range = $this->mathematics->arrayShuffle($this->task->getNodesStartRange($this->m));
        foreach ($this->ants as $ant) {
            $startNode = array_shift($range);
            $ant->newStart($startNode);
            $ant->findPath();
        }        
    }
    
    /**
     * Performing evaporation
     * 
     * @return void
     */
    protected function performEvaporation() : void {
        $count = count($this->edges);
        for ($i = 0; $i < $count; $i++) {
            for ($j = 0; $j < $count; $j++) {
                $edge = $this->edges[$i][$j];
                $edge->setPheromone($this->mathematics->evaporationFormula($edge->getPheromone(), $this->getP()));
            }            
        }
    }    
    
    /**
     * Creating all ants we need
     * 
     * @return void
     */
    protected function prepareAnts() : void {
        for ($i = 0; $i < $this->m; $i++) {
            $this->ants[] = $this->createAnt($i);
        }
    }
    
    /**
     * Put ant pheromone on path
     * 
     * @param array $path
     * @param float $distance
     * @param float $multiple How much to multiply the amount of pheromone we laid on path
     * @return void
     */
    protected function putAntPheromone(array $path, float $distance, float $multiple = 1) : void {
        if ($distance >= $this->closed_path_value) {
            return;
        }
        $count = count($path);
        for ($i = 0; $i < $count - 1 ; $i++) {
            $ind1 = $path[$i];
            $ind2 = $path[$i + 1];
            if (isset($this->edges[$ind1][$ind2] )) {
                $edge = $this->edges[$ind1][$ind2];
                $edge->setPheromone($edge->getPheromone() + $multiple * $this->mathematics->incrementPheromoneAmount($this->getQ(), $distance));			
            } 
        }       
    }
    
    /**
     * Checking the path if it is the best we found
     * 
     * @param array $path  Notice that path which comes here is normalized some way, 
     * so it is not confused with it's dublicates. See Task::findPath()
     * @param float $distance
     * @param int $i
     * @return void
     * @throws \LogicException
     */
    protected function checkBestPath(array $path, float $distance, int $i) : void {
        if ($distance > $this->closed_path_value) {
            return;
        }
        $key = implode('_', $path);
        if (($this->bestPath['distance'] <= $distance) || ($this->bestPath['key'] == $key)) {
            return;
        }
        $this->bestPath['key'] = $key;
        $this->bestPath['distance'] = $distance;
        $this->bestPath['path'] = $path;
        $this->history[$i] = [
            'distance' => $distance,
            'inner_path' => $key,
            'iteration' => $i,
            'time_spent' => $this->getTimeFromStart() . ' sec',
        ];
    }
    
    /**
     * Checks that value is bigger than zero
     * 
     * @param float|int $val
     * @param string $what
     * @return void
     * @throws \LogicException
     */
    protected function guardPositive(float|int $val, string $what) : void {
        if ($val <= 0) {
            throw new \LogicException("$what has to be positive number");
        }
    }
    
    /**
     * Creating a node
     * 
     * @param int $number
     * @return \mgrechanik\aco\classic\Node
     */
    protected function createNode(int $number) : Node {
        return new Node($number, $this);
    }

    /**
     * Creating an ant
     * 
     * @param int $number
     * @return \mgrechanik\aco\classic\Ant
     */
    protected function createAnt(int $number) : Ant {
        return new Ant($number, $this);
    }    
    
    /**
     * Creating an edge between nodes
     * 
     * @param float $distance
     * @param float $pheromone
     * @return \mgrechanik\aco\classic\Edge
     */
    protected function createEdge(float $distance, float $pheromone) : Edge {
        return new Edge($distance, $pheromone);
    }          
}

