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
 * This loader allows to use sets of data from this library:
 * 
 * http://comopt.ifi.uni-heidelberg.de/software/TSPLIB95/
 * 
 * This library gives sets of data and best result ever found for this data. 
 * Tour and distance.
 * 
 * Nowadays we support two types of files:
 * - Files with X and Y coordinates and Euclidean distances. Example - tests/fixtures/tsplib/berlin52.tsp 
 * - File with full matrix with explicit distances. Example - tests/fixtures/tsplib/bays29.tsp 
 */
class TspLibLoader
{
    /**
     * Loading Cities from file with next lines at header:
     * TYPE: TSP
     * EDGE_WEIGHT_TYPE: EUC_2D
     * 
     * @param srting $filePath Path to a data file
     * @return \mgrechanik\aco\City[] array of cities
     * @throws \Exception
     */
    public static function loadCitiesFromEuc2dFile(string $filePath) {
        $file = file($filePath);
        $cities = [];
        $formatOk = false;
        $start = false;
        foreach ($file as $row) {
            if (str_starts_with($row, 'TYPE')) {
                $parts = explode(':', trim($row));
                if (isset($parts[1]) && (trim($parts[1]) != 'TSP')) {
                    break;
                }
            }            
            if (str_contains($row, 'EDGE_WEIGHT_TYPE')) {
                $parts = explode(':', trim($row));
                if (isset($parts[1]) && (trim($parts[1]) == 'EUC_2D')) {
                    $formatOk = true;
                }
            }
            if (str_contains($row, 'NODE_COORD_SECTION')) {
                $start = true;
                continue;
            }
            if ($formatOk && $start) {
                $m = explode(' ', trim($row));
                $m2 = array_filter($m, function($val){return $val !== '';});
                $m3 = array_map('floatval', array_values($m2));
                if (isset($m3[0], $m3[1], $m3[2])) {
                    $cities[] = new City((float) $m3[1], (float) $m3[2], (string) $m3[0]);				
                } else {
                    $start = false;
                }
            }
        }
        if (!$formatOk) {
            throw new \Exception('Not correct EUC_2D file');
        }
        return $cities;

    }  
    
    /**
     * Loading matrix from file with next lines at header:
     * TYPE: TSP
     * EDGE_WEIGHT_TYPE: EXPLICIT
     * EDGE_WEIGHT_FORMAT: FULL_MATRIX 
     * 
     * @param string $filePath Path to a data file
     * @return array
     * @throws \Exception
     */
    public static function loadMatrixFromExplicitMatrixFile(string $filePath) {
        $file = file($filePath);
        $matrix = [];
        $formatOk = false;
        $formatOk2 = false;
        $start = false;
        $size = null;
        foreach ($file as $row) {
            if (str_starts_with($row, 'TYPE')) {
                $parts = explode(':', trim($row));
                if (isset($parts[1]) && (trim($parts[1]) != 'TSP')) {
                    break;
                }
            }             
            if (str_contains($row, 'EDGE_WEIGHT_TYPE')) {
                $parts = explode(':', trim($row));
                if (isset($parts[1]) && (trim($parts[1]) == 'EXPLICIT')) {
                    $formatOk = true;
                }
            }
            if (str_contains($row, 'EDGE_WEIGHT_FORMAT')) {
                $parts = explode(':', trim($row));
                if (isset($parts[1]) && (trim($parts[1]) == 'FULL_MATRIX')) {
                    $formatOk2 = true;
                }
            }            
            if (str_contains($row, 'EDGE_WEIGHT_SECTION')) {
                $start = true;
                continue;
            }
            if ($formatOk && $formatOk2 && $start) {
                $m = explode(' ', trim($row));
                $m2 = array_filter($m, function($val){return $val !== '';});
                $m3 = array_map('floatval', array_values($m2));
                $size = $size ?? count($m3);
                if ($size != count($m3)) {
                    break;
                }
                $matrix[] = $m3;				
            }
        }
        if (! ($formatOk && $formatOk2)) {
            throw new \Exception('Not correct EXPLICIT MATRIX file');
        }
        return $matrix;
    } 
}
