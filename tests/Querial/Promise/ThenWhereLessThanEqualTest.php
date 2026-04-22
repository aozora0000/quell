<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\Querial\WithEloquentModelTestCase;

final class ThenWhereLessThanEqualTest extends WithEloquentModelTestCase
{
    #[Test]
    public function リクエストにキーが存在する場合_lessthanequa_lクエリを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['price' => '1']);

        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereLessThanEqual('price');
        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`price` <= '1'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->resolve($request, $builder)));
    }
}
