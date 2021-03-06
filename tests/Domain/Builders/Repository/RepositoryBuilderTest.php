<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Generator\Tests\Domain\Builders\Repository;

use FlexPHP\Generator\Domain\Builders\Repository\RepositoryBuilder;
use FlexPHP\Generator\Tests\TestCase;
use FlexPHP\Schema\Schema;
use FlexPHP\Schema\SchemaAttribute;

final class RepositoryBuilderTest extends TestCase
{
    public function testItRenderSort(): void
    {
        $render = new RepositoryBuilder(new Schema('Test', 'bar', []), [
            'index',
            'create',
            'read',
            'update',
            'delete',
            'login',
            'other'
        ]);

        $this->assertEquals(<<<'T'
<?php declare(strict_types=1);

namespace Domain\Test;

use Domain\Test\Request\CreateTestRequest;
use Domain\Test\Request\DeleteTestRequest;
use Domain\Test\Request\IndexTestRequest;
use Domain\Test\Request\LoginTestRequest;
use Domain\Test\Request\ReadTestRequest;
use Domain\Test\Request\UpdateTestRequest;
use FlexPHP\Repositories\Repository;

/**
 * @method TestGateway getGateway
 */
final class TestRepository extends Repository
{
    /**
     * @return array<Test>
     */
    public function findBy(IndexTestRequest $request): array
    {
        return array_map(function (array $test) {
            return (new TestFactory())->make($test);
        }, $this->getGateway()->search((array)$request, [], $request->page, 50, $request->offset));
    }

    public function add(CreateTestRequest $request): Test
    {
        $test = (new TestFactory())->make($request);

        $test->setId($this->getGateway()->push($test));

        return $test;
    }

    public function getById(ReadTestRequest $request): Test
    {
        $factory = new TestFactory();
        $data = $this->getGateway()->get($factory->make($request));

        return $factory->make($data);
    }

    public function change(UpdateTestRequest $request): Test
    {
        $test = (new TestFactory())->make($request);

        $this->getGateway()->shift($test);

        return $test;
    }

    public function remove(DeleteTestRequest $request): Test
    {
        $factory = new TestFactory();
        $data = $this->getGateway()->get($factory->make($request));

        $test = $factory->make($data);

        $this->getGateway()->pop($test);

        return $test;
    }

    public function getByLogin(LoginTestRequest $request): Test
    {
        $data = $this->getGateway()->getBy('email', $request->email);

        return (new TestFactory())->make($data);
    }
}

T
, $render->build());
    }

    public function testItRenderIndexOk(): void
    {
        $render = new RepositoryBuilder(new Schema('Test', 'bar', []), ['index']);

        $this->assertEquals(<<<'T'
<?php declare(strict_types=1);

namespace Domain\Test;

use Domain\Test\Request\IndexTestRequest;
use FlexPHP\Repositories\Repository;

/**
 * @method TestGateway getGateway
 */
final class TestRepository extends Repository
{
    /**
     * @return array<Test>
     */
    public function findBy(IndexTestRequest $request): array
    {
        return array_map(function (array $test) {
            return (new TestFactory())->make($test);
        }, $this->getGateway()->search((array)$request, [], $request->page, 50, $request->offset));
    }
}

T
, $render->build());
    }

    public function testItRenderCreateOk(): void
    {
        $render = new RepositoryBuilder($this->getSchemaStringAndBlameBy(), ['create']);

        $this->assertEquals(<<<'T'
<?php declare(strict_types=1);

namespace Domain\Test;

use Domain\Test\Request\CreateTestRequest;
use FlexPHP\Repositories\Repository;

/**
 * @method TestGateway getGateway
 */
final class TestRepository extends Repository
{
    public function add(CreateTestRequest $request): Test
    {
        $test = (new TestFactory())->make($request);

        $test->setCode($this->getGateway()->push($test));

        return $test;
    }
}

T
, $render->build());
    }

    public function testItRenderCreateAiOk(): void
    {
        $render = new RepositoryBuilder(new Schema('Test', 'bar', [
            new SchemaAttribute('key', 'integer', 'pk|ai|required'),
        ]), ['create']);

        $this->assertEquals(<<<'T'
<?php declare(strict_types=1);

namespace Domain\Test;

use Domain\Test\Request\CreateTestRequest;
use FlexPHP\Repositories\Repository;

/**
 * @method TestGateway getGateway
 */
final class TestRepository extends Repository
{
    public function add(CreateTestRequest $request): Test
    {
        $test = (new TestFactory())->make($request);

        $test->setKey($this->getGateway()->push($test));

        return $test;
    }
}

T
, $render->build());
    }

    public function testItRenderReadOk(): void
    {
        $render = new RepositoryBuilder(new Schema('Test', 'bar', []), ['read']);

        $this->assertEquals(<<<'T'
<?php declare(strict_types=1);

namespace Domain\Test;

use Domain\Test\Request\ReadTestRequest;
use FlexPHP\Repositories\Repository;

/**
 * @method TestGateway getGateway
 */
final class TestRepository extends Repository
{
    public function getById(ReadTestRequest $request): Test
    {
        $factory = new TestFactory();
        $data = $this->getGateway()->get($factory->make($request));

        return $factory->make($data);
    }
}

T
, $render->build());
    }

    public function testItRenderUpdateOk(): void
    {
        $render = new RepositoryBuilder(new Schema('Test', 'bar', []), ['update']);

        $this->assertEquals(<<<'T'
<?php declare(strict_types=1);

namespace Domain\Test;

use Domain\Test\Request\UpdateTestRequest;
use FlexPHP\Repositories\Repository;

/**
 * @method TestGateway getGateway
 */
final class TestRepository extends Repository
{
    public function change(UpdateTestRequest $request): Test
    {
        $test = (new TestFactory())->make($request);

        $this->getGateway()->shift($test);

        return $test;
    }
}

T
, $render->build());
    }

    public function testItRenderDeleteOk(): void
    {
        $render = new RepositoryBuilder(new Schema('Test', 'bar', []), ['delete']);

        $this->assertEquals(<<<'T'
<?php declare(strict_types=1);

namespace Domain\Test;

use Domain\Test\Request\DeleteTestRequest;
use FlexPHP\Repositories\Repository;

/**
 * @method TestGateway getGateway
 */
final class TestRepository extends Repository
{
    public function remove(DeleteTestRequest $request): Test
    {
        $factory = new TestFactory();
        $data = $this->getGateway()->get($factory->make($request));

        $test = $factory->make($data);

        $this->getGateway()->pop($test);

        return $test;
    }
}

T
, $render->build());
    }

    public function testItRenderLoginOk(): void
    {
        $render = new RepositoryBuilder(new Schema('User', 'bar', []), ['create', 'read', 'update', 'delete', 'login']);

        $this->assertEquals(<<<'T'
<?php declare(strict_types=1);

namespace Domain\User;

use Domain\User\Request\CreateUserRequest;
use Domain\User\Request\DeleteUserRequest;
use Domain\User\Request\LoginUserRequest;
use Domain\User\Request\ReadUserRequest;
use Domain\User\Request\UpdateUserRequest;
use FlexPHP\Repositories\Repository;

/**
 * @method UserGateway getGateway
 */
final class UserRepository extends Repository
{
    public function add(CreateUserRequest $request): User
    {
        $user = (new UserFactory())->make($request);

        if ($user->getPassword()) {
            $user->setPassword($this->getHashPassword($user->getPassword()));
        }

        $user->setId($this->getGateway()->push($user));

        return $user;
    }

    public function getById(ReadUserRequest $request): User
    {
        $factory = new UserFactory();
        $data = $this->getGateway()->get($factory->make($request));

        $data['password'] = $this->getFakePassword();

        return $factory->make($data);
    }

    public function change(UpdateUserRequest $request): User
    {
        $user = (new UserFactory())->make($request);

        if ($user->getPassword() && $user->getPassword() !== $this->getFakePassword()) {
            $user->setPassword($this->getHashPassword($user->getPassword()));
        }

        $this->getGateway()->shift($user);

        return $user;
    }

    public function remove(DeleteUserRequest $request): User
    {
        $factory = new UserFactory();
        $data = $this->getGateway()->get($factory->make($request));

        $user = $factory->make($data);

        $this->getGateway()->pop($user);

        return $user;
    }

    public function getByLogin(LoginUserRequest $request): User
    {
        $data = $this->getGateway()->getBy('email', $request->email);

        return (new UserFactory())->make($data);
    }

    private function getHashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private function getFakePassword(): string
    {
        return '**********';
    }
}

T
, $render->build());
    }

    public function testItRenderFkRelationsOk(): void
    {
        $render = new RepositoryBuilder($this->getSchemaFkRelation('PostComments'), ['index', 'update']);

        $this->assertEquals(<<<'T'
<?php declare(strict_types=1);

namespace Domain\PostComment;

use Domain\PostComment\Request\FindPostCommentBarRequest;
use Domain\PostComment\Request\FindPostCommentPostRequest;
use Domain\PostComment\Request\FindPostCommentUserStatusRequest;
use Domain\PostComment\Request\IndexPostCommentRequest;
use Domain\PostComment\Request\UpdatePostCommentRequest;
use FlexPHP\Repositories\Repository;

/**
 * @method PostCommentGateway getGateway
 */
final class PostCommentRepository extends Repository
{
    /**
     * @return array<PostComment>
     */
    public function findBy(IndexPostCommentRequest $request): array
    {
        return array_map(function (array $postComment) {
            return (new PostCommentFactory())->make($postComment);
        }, $this->getGateway()->search((array)$request, [], $request->page, 50, $request->offset));
    }

    public function change(UpdatePostCommentRequest $request): PostComment
    {
        $postComment = (new PostCommentFactory())->make($request);

        $this->getGateway()->shift($postComment);

        return $postComment;
    }

    public function findBarsBy(FindPostCommentBarRequest $request): array
    {
        return $this->getGateway()->filterBars($request, $request->page, 20);
    }

    public function findPostsBy(FindPostCommentPostRequest $request): array
    {
        return $this->getGateway()->filterPosts($request, $request->page, 20);
    }

    public function findUserStatusBy(FindPostCommentUserStatusRequest $request): array
    {
        return $this->getGateway()->filterUserStatus($request, $request->page, 20);
    }
}

T
, $render->build());
    }

    public function testItRenderBlameByOk(): void
    {
        $render = new RepositoryBuilder($this->getSchemaStringAndBlameBy(), ['index']);

        $this->assertEquals(<<<'T'
<?php declare(strict_types=1);

namespace Domain\Test;

use Domain\Test\Request\IndexTestRequest;
use FlexPHP\Repositories\Repository;

/**
 * @method TestGateway getGateway
 */
final class TestRepository extends Repository
{
    /**
     * @return array<Test>
     */
    public function findBy(IndexTestRequest $request): array
    {
        return array_map(function (array $test) {
            return (new TestFactory())->make($test);
        }, $this->getGateway()->search((array)$request, [], $request->page, 50, $request->offset));
    }
}

T
, $render->build());
    }

    /**
     * @dataProvider getEntityName
     */
    public function testItRenderWithDiffNameOk(string $entity, string $expected): void
    {
        $render = new RepositoryBuilder(new Schema($entity, 'bar', []), ['action']);

        $this->assertEquals(<<<T
<?php declare(strict_types=1);

namespace Domain\\{$expected};

use FlexPHP\Repositories\Repository;

/**
 * @method {$expected}Gateway getGateway
 */
final class {$expected}Repository extends Repository
{
}

T
, $render->build());
    }

    public function getEntityName(): array
    {
        return [
            ['userpassword', 'Userpassword'],
            ['USERPASSWORD', 'Userpassword'],
            ['UserPassword', 'UserPassword'],
            ['userPassword', 'UserPassword'],
            ['user_password', 'UserPassword'],
            ['Posts', 'Post'],
        ];
    }
}
