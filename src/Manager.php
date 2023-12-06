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

use mgrechanik\aco\distances\Euclidean;
use mgrechanik\aco\classic\Finder;
use mgrechanik\aco\classic\Mathematics;

/**
 * Class manager allows to set data we work with to system and runs process of finding best path.
 * 
 * Data could be loaded from cities - array of objects with X and Y coordinates each.
 * (Distances between cities could be calculated according chosen strategy)
 * 
 * Data could be also loaded explicitly from the Adjacency Matrix.
 * 
 * You can choose the task we are solving - Travelling salesman problem or Shortest path problem or your custom task.
 * 
 * Also you can choose ant strategy:
 *    - Classic Ant Colony Optimization Algorithm 
 *    - Elitist Ant Colony Optimization Algorithm
 *    - your own
 * 
 * Example 1:
 * // cities are identified by their key - 0, 1, 2, 3
 * $cities = [new City(10,10), new City(50,50), new City(10,50), new City(60,10)]
 * $manager = new Manager();
 * $manager->setCities(...$cities);
 * //By default we are solving Travelling salesman problem
 * $distance = $manager->run(400);
 * $innerPath = $manager->getInnerPath();
 * var_dump('distance = ' . $distance)
 * var_dump($innerPath)
 * 
 * We will get:
 * 
 *   distance = 171
 * 
 *   Array
 *   (
 *       [0] => 0
 *       [1] => 2
 *       [2] => 1
 *       [3] => 3
 *       [4] => 0
 *   )
 * 
 * Emample 2:
 * // Using Elitist finder
 * $finder = new \mgrechanik\aco\elitist\Finder();
 * // Adjust it
 * $finder->setSigmaPercent(150);
 * // Using with Manager
 * $manager = new Manager(finder : $finder);
 * ...
 */
class Manager
{
    /**
     * @var array An array of cities 
     */
    protected array $cities = [];
    
    /**
     * @var array Index of city names to their inner indexes 
     * In the inner work we identify all nodes as numbers - 0, 1, 2, ... 
     * But from outside you can give names of nodes and get results with this names instead of inner numbers
     */    
    protected array $nameIndex = [];
    
    /**
     * @var array An Adjacency Matrix of all our nodes.
     * This matrix is the basic data structure for our system to work with.
     * 
     * It has N rows, and each of them has N columns.
     * Indexes are the numbers from 0 to N-1.
     * By these indexes our nodes are identified.
     * Inner path consists of them.
     * On the intersection row I and column J we keep distance between these two nodes.
     */    
    protected array $matrix = [];
    
    /**
     * @var ?float Result distance 
     */    
    protected ?float $distance = null;
    
    /**
     * @var array Result inner path, values are the ones we use in matrix (it's keys) 
     */    
    protected array $inner_path = [];

    /**
     * @var DistanceInterface Strategy to calculate distances between cities 
     */
    protected DistanceInterface $distanceStrategy;
    
    /**
     * @var AFinder Finder we are using to make search work. 
     * By default we use classic aco algorithm.
     * But can change it to Elitist aco alorithm or your custom implementation 
     */    
    protected AFinder $finder;
    
    /**
     * @var MathematicsInterface Strategy to do mathematic tasks 
     */    
    protected MathematicsInterface $mathematics;
    
    /**
     * @var Task The task we are solving. 
     * By default we are solving TSP.
     * But can change it to SPP task or any your own task
     */    
    protected Task $task;
    
    /**
     * Constructor 
     * 
     * @param Euclidean $distanceStrategy
     * @param \mgrechanik\aco\AFinder $finder
     * @param \mgrechanik\aco\MathematicsInterface $mathematics
     * @param \mgrechanik\aco\Task $task
     */
    public function __construct(DistanceInterface $distanceStrategy = null, AFinder $finder = null, 
                                MathematicsInterface $mathematics = null, Task $task = null) {
        if (!$distanceStrategy) {
            $distanceStrategy = new Euclidean();
        }
        $this->distanceStrategy = $distanceStrategy;
        if (!$finder) {
            $finder = new Finder();
        }
        $this->finder = $finder;  
        $this->finder->setManager($this);
        if (!$mathematics) {
            $mathematics = new Mathematics();
        }
        $this->mathematics = $mathematics;  
        $finder->setMathematics($mathematics);
        if (!$task) {
            $task = new TspTask();
        }
        $this->task = $task;  
        $finder->setTask($task);        
    }

    /**
     * Returns finder
     * 
     * @return \mgrechanik\aco\AFinder|null
     */
    public function getFinder(): ?AFinder {
        return $this->finder;
    }
    
    /**
     * Resulting inner path
     * 
     * @return array
     */
    public function getInnerPath() : array {
        return $this->inner_path;
    }
    
    /**
     * Returns named path.
     * 
     * Named path are useful if you set some names to your nodes, different from inner names of nodes, used in matrix (0, 1, 2, ...)
     * @return array
     */
    public function getNamedPath() : array {
        return $this->getNamedFromIndexedPath($this->inner_path);
    }    

    /**
     * Result distance
     * 
     * @return float
     */
    public function getDistance() : ?float {
        return $this->distance;
    }  
    
    /**
     * Return Adjacency Matrix we work with
     * 
     * @return array
     */
    public function getMatrix() : array {
        return $this->matrix;
    }    
    
    /**
     * Return cities
     * 
     * @return array
     */
    public function getCities() : array {
        return $this->matrix;
    }     

    /**
     * Setting cities to the system
     * From them we will build self::matrix
     * 
     * @param \mgrechanik\aco\City[] $cities
     * @return void
     * @throws \LogicException
     */
    public function setCities(City ...$cities) : void {
        $this->cities = array_values($cities);
        foreach ($this->cities as $key => $city) {
            $city->name = $city->name ?: strval($key); 
            if (isset($this->nameIndex[$city->name])) {
                throw new \LogicException('Names of the cities should be unique');
            }
            $this->nameIndex[$city->name] = $key;
        }
        $this->buildMatrixFromCities();
    }

    /**
     * Building Adjacency matrix from an array of cities
     * 
     * @return void
     */
    protected function buildMatrixFromCities() : void {
        $this->matrix = [];
        $cities = $this->cities;
        $count = count($cities);
        $res = [];
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                if (!isset($res[$i])) {
                    $res[$i] = [];
                }
                if (!isset($res[$j])) {
                    $res[$j] = [];
                }	
                $distance = (float) $this->distanceStrategy->distance($cities[$i]->x, $cities[$i]->y, $cities[$j]->x, $cities[$j]->y);
                $res[$i][$j] = $res[$j][$i] = $distance;
            }
            $res[$i][$i] = 0;
        }
        $this->matrix = $res;        
    }
    
    /**
     * Explicitly setting Adjacency matrix to system
     * 
     * @param array $matrix Two dimension array with keys from 0 to n-1, where n - amount of nodes
     * @param int $nameStart PHP arrays start with 0, but if you want to name your nodes differently set the starting int value of name
     * For example TSPLIB name their nodes from 1 when you load data from their matrix
     * @return void
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function setMatrix(array $matrix, int $nameStart = 0) : void {
        if (!empty($this->matrix)) {
            throw new \LogicException('Matrix was already built from cities');
        }
        $count = count($matrix);
        $name = $nameStart - 1;
        for ($i = 0; $i < $count; $i++) {
            $this->matrix[$i] = [];
            for ($j = 0; $j < $count; $j++) {
                if (!isset($matrix[$i][$j]) || !is_scalar($matrix[$i][$j])) {
                    throw new \InvalidArgumentException('Matrix format is wrong');
                }
                if (($i != $j) && ($matrix[$i][$j] <= 0)) {
                    throw new \InvalidArgumentException('Matrix should consist of positive numbers bigger than zero');
                }
                $this->matrix[$i][$j] = (float) $matrix[$i][$j];
            }            
            $name++;
            $this->cities[$i] = new City(-1, -1, strval($name));
            $this->nameIndex[$name] = $i;
        }

    }
    
    /**
     * Updating Adjacency matrix.
     * Useful if you ever need to do some manual change. Close some paths for example.
     * 
     * $param int $y First dimension of the matrix
     * $param int $x Second dimension of the matrix
     * $param float|int $value New value
     * $param bool $double Whether to make double change
     * @return void
     * @throws \InvalidArgumentException
     */
    public function updateMatrix(int $y, int $x, float|int $value, bool $double = true) : void {
        if (isset($this->matrix[$y][$x], $this->matrix[$x][$y])) {
            if (($x != $y) && ($value <= 0)) {
                throw new \InvalidArgumentException('Matrix should consist of positive numbers bigger than zero');
            }
            $this->matrix[$y][$x] = (float) $value;
            if ($double) {
                $this->matrix[$x][$y] = (float) $value;
            }
        } else {
            throw new \InvalidArgumentException('Wrong matrix indexes');
        }
    }
    
    /**
     * Return distance of a path given
     * 
     * @param array $path
     * @param bool $fromNames Whether path consist of names of the nodes or their inner indexes
     * @return float
     * @throws \LogicException
     */
    public function countPath(array $path, bool $fromNames = false) : float {
        $sum = 0;
        $count = count($path);
        if ($count < 2) {
            throw new \LogicException('Path needs to be represented by at least two cities');
        }
        if (empty($this->matrix)) {
            throw new \LogicException('You need to set up cities or matrix before counting path');
        }        
        $path = array_values($path);
        if ($fromNames) {
            $path = $this->getIndexedFromNamedPath($path);
        }        
        for ($i = 0; $i < $count - 1 ; $i++) {
            $ind1 = $path[$i];
            $ind2 = $path[$i + 1];
            if (isset($this->matrix[$ind1][$ind2])) {
                $sum += $this->matrix[$ind1][$ind2];				
            } else {
                throw new \LogicException('Path consists of wrong names');
            }
        }
        return $sum;
    }     
    
    /**
     * Runs the process of solving a task of finding best tour
     * 
     * @param int $iterations Amount of iterations to do a search
     * @return float|null Distance
     * @throws \LogicException
     */
    public function run(int $iterations = 400) : ?float {
        $this->distance = null;
        $this->inner_path = [];
        if (empty($this->matrix)) {
            throw new \LogicException('You need to set some data to process with');
        }
        $finder = $this->finder;
        $finder->setIterations($iterations);
        $res = $finder->run($this->matrix, $iterations);
        if (isset($res['path'], $res['distance'])) {
            if (empty($res['path'])) {
                $res['distance'] = null;
                $this->inner_path = [];
            } else {
                $this->inner_path = $res['path'];
            }
            return $this->distance = $res['distance'];
        } 
        return null;
    }
    
    /**
     * Returns an array of named paths from inner ones
     * 
     * @param array $path inner paths
     * @return array
     * @throws \LogicException
     */
    public function getNamedFromIndexedPath(array $path) : array {
        $res = [];
        foreach ($path as $key => $val) {
            if (!isset($this->cities[$val])) {
                throw new \LogicException('Inner Path consists of wrong indexes');
            }
            $city = $this->cities[$val];
            $res[$key] = $city->name;
        }
        return $res;
    }
    
    /**
     * Returns an array of inner paths from named ones
     * 
     * @param array $path
     * @return array
     * @throws \LogicException
     */
    public function getIndexedFromNamedPath(array $path) : array {
        $res = [];
        foreach ($path as $key => $val) {
            if (isset($this->nameIndex[$val])) {
                $res[$key] = $this->nameIndex[$val];
            } else {
                throw new \LogicException('Named Path consists of wrong names');
            }
        }
        return $res;
    }    

}
