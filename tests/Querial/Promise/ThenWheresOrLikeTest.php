<?php

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Formatter\LikeFormatter;
use Querial\Promise\Support\ThenWherePromisesAggregator;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWheresOrLikeTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    #[Test]
    public function コンストラクターのテスト()
    {
        $table = 'users';
        $formatter = LikeFormatter::PARTIAL_MATCH;
        $instance = new ThenWheresOrLike(['name', 'email'], 'John', $table, $formatter);

        $this->assertInstanceOf(ThenWheresOrLike::class, $instance);
    }

    /**
     * @test
     */
    #[Test]
    public function マッチ関数が正しいを返すテスト()
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);

        $instance = new ThenWheresOrLike(['name', 'email'], 'name');
        $result = $instance->match($request);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    #[Test]
    public function リゾルブ関数が正しいビルダーを返すテスト()
    {
        $request = Request::create('/', 'GET', ['search' => 'test']);
        $model = $this->createModel();
        $query = $model->newQuery();
        $instance = new ThenWheresOrLike(['name', 'email'], 'search');
        $result = $instance->resolve($request, $query);

        $this->assertInstanceOf(Builder::class, $result);
        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  (
    `users`.`name` like '%test%'
    or `users`.`email` like '%test%'
  )
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }

    /**
     * @test
     */
    #[Test]
    public function 複合した時に正しいビルダーを返すテスト()
    {
        $request = Request::create('/', 'GET', [
            'email' => 'email@email.com',
            'search' => 'test',
        ]);
        $model = $this->createModel();
        $query = $model->newQuery();
        $instance = new ThenWherePromisesAggregator([
            new ThenWhereEqual('email'),
            new ThenWheresOrLike(['name', 'column1'], 'search'),
        ]);
        $result = $instance->resolve($request, $query);

        $this->assertInstanceOf(Builder::class, $result);
        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  (
    `users`.`email` = 'email@email.com'
    and (
      `users`.`name` like '%test%'
      or `users`.`column1` like '%test%'
    )
  )
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }
}
