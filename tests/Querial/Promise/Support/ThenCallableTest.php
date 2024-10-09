<?php

namespace Tests\Querial\Promise\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Promise\Support\ThenCallable;
use Tests\Querial\WithEloquentModelTestCase;

class ThenCallableTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    public function 即時関数の条件に一致した場合、即時関数内のクエリが実行される(): void
    {
        $request = Request::create('/', 'GET', ['mode' => 'search', 'name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = (new ThenCallable(function (Request $request) {
            return $request->has('mode') && $request->input('mode') === 'search';
        }, function (Request $request, Builder $builder) {
            return $builder->where('name', 'LIKE', 'test%');
        }));
        $this->assertTrue($instance->match($request));
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "name" LIKE 'test%'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->resolve($request, $query)));
    }

    /**
     * @test
     */
    public function 即時関数の条件に一致しない場合、即時関数内のクエリは実行されない(): void
    {
        $request = Request::create('/', 'GET', ['mode' => 'normal', 'name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = (new ThenCallable(function (Request $request) {
            return $request->has('mode') && $request->input('mode') === 'search';
        }, function (Request $request, Builder $builder) {
            return $builder->where('name', 'LIKE', 'test%');
        }));
        $this->assertNotTrue($instance->match($request));
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->resolve($request, $query)));
    }
}
