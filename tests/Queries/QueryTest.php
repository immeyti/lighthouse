<?php

namespace Nuwave\Relay\Tests\Queries;

use GraphQL;
use Nuwave\Relay\Support\Definition\GraphQLQuery;
use Nuwave\Relay\Tests\Support\Models\User;
use Nuwave\Relay\Tests\Support\GraphQL\Types\UserType;
use Nuwave\Relay\Tests\Support\GraphQL\Types\TaskType;
use Nuwave\Relay\Tests\Support\GraphQL\Queries\UserQuery;
use Nuwave\Relay\Tests\TestCase;

class QueryTest extends TestCase
{
    /**
     * @test
     */
    public function itCanExecuteQuery()
    {
        $query = '{
            userQuery {
                name
            }
        }';

        $expected = [
            'userQuery' => [
                'name' => 'foo'
            ]
        ];

        $graphql = app('graphql');
        $graphql->schema()->type('user', UserType::class);
        $graphql->schema()->type('task', TaskType::class);
        $graphql->schema()->query('userQuery', UserQuery::class);

        $this->assertEquals(['data' => $expected], $this->executeQuery($query));
    }

    /**
     * @test
     */
    public function itCanExecuteConnectionQuery()
    {
        $query = '{
            userQuery {
                name
                tasks(order: "DESC") {
                    edges {
                        node {
                            title
                        }
                    }
                }
            }
        }';

        $graphql = app('graphql');
        $graphql->schema()->type('user', UserType::class);
        $graphql->schema()->type('task', TaskType::class);
        $graphql->schema()->query('userQuery', UserQuery::class);

        $data = $this->executeQuery($query)['data'];
        $this->assertEquals('foo', array_get($data, 'userQuery.name'));
        $this->assertCount(5, array_get($data, 'userQuery.tasks.edges', []));
        $this->assertEquals('foo', array_first(array_pluck(array_get($data, 'userQuery.tasks.edges', []), 'node.title')));
    }
}