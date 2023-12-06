<?php
/**
 * This file is part of the mgrechanik/ant-colony-optimization library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/ant-colony-optimization/blob/main/LICENSE.md
 * @link https://github.com/mgrechanik/ant-colony-optimization
 */

declare(strict_types=1);

namespace mgrechanik\aco\tests;

use mgrechanik\aco\classic\Node;
use mgrechanik\aco\AFinder;

class NodeTest extends \PHPUnit\Framework\TestCase
{
    public function testPheromonFormula() {
        $finder = $this->createStub(AFinder::class);
        $node = new Node(1, $finder);
        
        $this->assertEquals(1, $node->getNumber());
    }
}
