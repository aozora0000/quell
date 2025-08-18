<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenJoinIf;
use Tests\Querial\WithEloquentModelTestCase;

class ThenJoinIfTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 許可されたjoinが適用される(): void
    {
        // join=items → join `items` on `items`.`user_id` = `users`.`id`
        $request = Request::create('/', 'GET', ['join' => 'items']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $allowed = [
            'items' => [
                'table' => 'items',
                'first' => 'items.user_id',
                'operator' => '=',
                'second' => 'users.id',
                'type' => 'inner',
            ],
        ];
        $instance = new ThenJoinIf($allowed);

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
  INNER JOIN `items` ON `items`.`user_id` = `users`.`id`
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), 'JOINのクエリが一致しません');
    }
}
