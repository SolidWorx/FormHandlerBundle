<?php
/**
 * This file is part of the FormHandlerBundle project.
 *
 * @author     pierre
 * @copyright  Copyright (c) 2017
 */

namespace SolidWorx\FormHandler\Tests;

use PHPUnit\Framework\TestCase;
use SolidWorx\FormHandler\Options;

class OptionsTest extends TestCase
{
    public function testGet()
    {
        $options = Options::fromArray(['a' => 'b']);

        $this->assertSame('b', $options->get('a'));
        $this->assertSame('b', $options['a']);
    }

    public function testGetWithDefault()
    {
        $options = Options::fromArray(['a' => 'b']);

        $this->assertNull($options->get('c'));
        $this->assertNull($options['c']);
    }

    public function testMutable()
    {
        $options = Options::fromArray(['a' => 'b']);

        $this->expectException(\LogicException::class);

        $options['c'] = 'd';
    }

    public function testMerge()
    {
        $options = Options::fromArray(['a' => 'b'])->merge(['c' => 'd']);

        $this->assertSame('b', $options->get('a'));
        $this->assertSame('b', $options['a']);
        $this->assertSame('d', $options->get('c'));
        $this->assertSame('d', $options['c']);
    }
}
