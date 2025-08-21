<?php

namespace Tests\Querial\Helper;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Querial\Helper\Str;

class StrTest extends TestCase
{
    // 真偽値ヘルパー
    #[Test]
    public function 真偽値_true系を判定できる(): void
    {
        // 'yes' は真として判定される
        $this->assertTrue(Str::isTruthy('yes'), 'yes は真として判定されるべきです');
    }

    #[Test]
    public function 真偽値_false系を判定できる(): void
    {
        // 'no' は偽として判定される
        $this->assertTrue(Str::isFalsy('no'), 'no は偽として判定されるべきです');
    }


    // 配列/分割ヘルパー
    #[Test]
    public function 文字列を区切りで分割し_trimと空要素除外ができる(): void
    {
        // ' , a , , b ' は ['a','b'] になる
        $expected = ['a', 'b'];
        $actual = Str::splitToList(' , a , , b ');
        $this->assertSame($expected, $actual, 'trim/空要素除外の結果が期待と異なります');
    }

    #[Test]
    public function 分割要素をintへキャストできる(): void
    {
        // '1, 2, ,3' は [1,2,3] になる
        $expected = [1, 2, 3];
        $actual = Str::splitToList('1, 2, ,3', ',','int');
        $this->assertSame($expected, $actual, 'intキャストの結果が期待と異なります');
    }

    #[Test]
    public function 数値をSQL文字列にクォートできる(): void
    {
        // 12.340000 は '12.34' としてクォートされる
        $expected = "'12.34'";
        $actual = Str::quoteNumber(12.34);
        $this->assertSame($expected, $actual, '数値のSQLクォート結果が期待と異なります');
    }
}
