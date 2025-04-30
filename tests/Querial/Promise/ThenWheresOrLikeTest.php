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
    #[Test]
    public function コンストラクターのテスト(): void
    {
        $table = 'users';
        $formatter = LikeFormatter::PARTIAL_MATCH;
        $instance = new ThenWheresOrLike(['name', 'email'], 'John', $table, $formatter);

        $this->assertInstanceOf(ThenWheresOrLike::class, $instance);
    }

    #[Test]
    public function マッチ関数が正しいを返すテスト(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);

        $instance = new ThenWheresOrLike(['name', 'email'], 'name');
        $result = $instance->match($request);

        $this->assertTrue($result);
    }

    #[Test]
    public function リゾルブ関数が正しいビルダーを返すテスト(): void
    {
        $request = Request::create('/', 'GET', ['search' => 'test']);
        $model = $this->createModel();
        $builder = $model->newQuery();
        $instance = new ThenWheresOrLike(['name', 'email'], 'search');
        $result = $instance->resolve($request, $builder);

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
        $this->assertSame(mb_strtolower($sql), $this->format($builder));
    }

    #[Test]
    public function 複合した時に正しいビルダーを返すテスト(): void
    {
        $request = Request::create('/', 'GET', [
            'email' => 'email@email.com',
            'search' => 'test',
        ]);
        $model = $this->createModel();
        $builder = $model->newQuery();
        $instance = new ThenWherePromisesAggregator([
            new ThenWhereEqual('email'),
            new ThenWheresOrLike(['name', 'column1'], 'search'),
        ]);
        $result = $instance->resolve($request, $builder);

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
        $this->assertSame(mb_strtolower($sql), $this->format($builder));
    }
}
