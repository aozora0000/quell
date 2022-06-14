<?php

namespace Test\Querial;

use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\SQLiteConnection;
use PDO;
use PHPStan\Testing\TestCase;
use Test\Querial\MockModel\User;

class WithEloquentModelTestCase extends TestCase
{
    protected ConnectionResolverInterface $connection;
    public function setUp(): void
    {
        \Illuminate\Database\Query\Builder::macro('toRawSql', function(){
            return array_reduce($this->getBindings(), function($sql, $binding){
                return preg_replace('/\?/', is_numeric($binding) ? $binding : "'".$binding."'" , $sql, 1);
            }, $this->toSql());
        });
        \Illuminate\Database\Eloquent\Builder::macro('toRawSql', function(){
            return ($this->getQuery()->toRawSql());
        });


        $this->connection = new ConnectionResolver([
            'default' => new SQLiteConnection(new PDO('sqlite::memory:')),
        ]);
        $this->connection->setDefaultConnection('default');


        parent::setUp(); // TODO: Change the autogenerated stub
    }

    protected function createModel(array $attributes = []): Model
    {
        $model = new User();
        $model->setConnectionResolver($this->connection);
        return $model;
    }
}