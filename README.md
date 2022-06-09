# LaravelQuerial
PromiseLike Chains IlluminateRequest to QueryBuilder

## How To Use
```controller.php
public function get(\Illuminate\Http\Request $request)
{
    $pipeline = new Pipeline($request);
    $pipeline
         // where name = `{request.name}` AND
        ->then(new ThenWhereEqualWithQuery('name'))
        
         // where comment_id IN (`{request.comment_id}`) AND
        ->then(new ThenWhereInArrayWithQuery('comment_id'))
        
        // where exists (
        //    select * from users where users.id = posts.id AND
        //    (
        //       where users.name LIKE '%{request.user.name}%' OR
        //       where users.email LIKE '%{request.user.email}%'
        //    )
        // )
        ->then(new ThenWhereHasRelation('user', new ThenWherePromisesAggregator([
            new ThenOrWhereLikeWithQuery('name', 'user.name'),
            new ThenOrWhereLikeWithQuery('email', 'user.email'),
        ])))
        ->build(Post::with(['user']));
}
```