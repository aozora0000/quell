<?php

namespace Tests\Querial\Formatter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Querial\Formatter\LikeFormatter;

class LikeFormatterTest extends TestCase
{
    #[Test]
    public function 前方一致(): void
    {
        $formatter = LikeFormatter::FORWARD_MATCH;
        $this->assertSame('hello%', $formatter->format('hello'));
    }

    #[Test]
    public function 後方一致(): void
    {
        $formatter = LikeFormatter::BACKWARD_MATCH;
        $this->assertSame('%hello', $formatter->format('hello'));
    }

    #[Test]
    public function 部分一致(): void
    {
        $formatter = LikeFormatter::PARTIAL_MATCH;
        $this->assertSame('%hello%', $formatter->format('hello'));
    }

    #[Test]
    public function 完全一致(): void
    {
        $formatter = LikeFormatter::EXACT_MATCH;
        $this->assertSame('hello', $formatter->format('hello'));
    }
}
