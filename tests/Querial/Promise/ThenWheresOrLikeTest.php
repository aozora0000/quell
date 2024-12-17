<?php

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Formatter\LikeFormatter;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWheresOrLikeTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    #[Test]
    public function コンストラクターのテスト()
    {
        $attribute = 'name';
        $inputTarget = ['John', 'Jane'];
        $table = 'users';
        $formatter = LikeFormatter::PARTIAL_MATCH;

        $instance = new ThenWheresOrLike($attribute, $inputTarget, $table, $formatter);

        $this->assertInstanceOf(ThenWheresOrLike::class, $instance);
    }

    /**
     * @test
     */
    #[Test]
    public function マッチ関数が正しいを返すテスト()
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);

        $instance = new ThenWheresOrLike('name', ['column1', 'column2']);
        $result = $instance->match($request);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    #[Test]
    public function リゾルブ関数が正しいビルダーを返すテスト()
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();
        $instance = new ThenWheresOrLike('name', ['column1', 'column2']);
        $result = $instance->resolve($request, $query);

        $this->assertInstanceOf(Builder::class, $result);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  (
    "users"."name" LIKE '%test%'
    OR "users"."name" LIKE '%test%'
  )
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }
}
