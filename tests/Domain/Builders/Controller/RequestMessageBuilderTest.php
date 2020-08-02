<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Generator\Tests\Domain\Builders\Controller;

use FlexPHP\Generator\Domain\Builders\Controller\RequestMessageBuilder;
use FlexPHP\Generator\Tests\TestCase;
use FlexPHP\Schema\Schema;
use FlexPHP\Schema\SchemaAttribute;

final class RequestMessageBuilderTest extends TestCase
{
    /**
     * @dataProvider getEntityName
     */
    public function testItRenderOk(string $entity, string $expected): void
    {
        $render = new RequestMessageBuilder(new Schema($entity, 'bar', []), 'index');

        $this->assertEquals(<<<T
        \$request = new Index{$expected}Request(\$request->request->all());
T
, $render->build());
    }

    public function testItRenderIndexOk(): void
    {
        $render = new RequestMessageBuilder(new Schema('Test', 'bar', []), 'index');

        $this->assertEquals(<<<T
        \$request = new IndexTestRequest(\$request->request->all());
T
, $render->build());
    }

    public function testItRenderCreateOk(): void
    {
        $render = new RequestMessageBuilder(new Schema('Test', 'bar', []), 'create');

        $this->assertEquals(<<<T
        \$form = \$this->createForm(TestFormType::class);
        \$form->handleRequest(\$request);

        \$request = new CreateTestRequest(\$form->getData());
T
, $render->build());
    }

    public function testItRenderCreateBlameByOk(): void
    {
        $render = new RequestMessageBuilder(new Schema('Test', 'bar', [
            new SchemaAttribute('Pk', 'integer', 'cb'),
        ]), 'create');

        $this->assertEquals(<<<T
        \$form = \$this->createForm(TestFormType::class);
        \$form->handleRequest(\$request);

        \$request = new CreateTestRequest(\$form->getData(), \$this->getUser()->pk());
T
, $render->build());
    }

    public function testItRenderReadOk(): void
    {
        $render = new RequestMessageBuilder(new Schema('Test', 'bar', []), 'read');

        $this->assertEquals(<<<T
        \$request = new ReadTestRequest(\$id);
T
, $render->build());
    }

    public function testItRenderUpdateOk(): void
    {
        $render = new RequestMessageBuilder(new Schema('Test', 'bar', []), 'update');

        $this->assertEquals(<<<T
        \$form = \$this->createForm(TestFormType::class);
        \$form->submit(\$request->request->get(\$form->getName()));
        \$form->handleRequest(\$request);

        \$request = new UpdateTestRequest(\$id, \$form->getData());
T
, $render->build());
    }

    public function testItRenderUpdateBlameByOk(): void
    {
        $render = new RequestMessageBuilder(new Schema('Test', 'bar', [
            new SchemaAttribute('Key', 'integer', 'ub'),
        ]), 'update');

        $this->assertEquals(<<<T
        \$form = \$this->createForm(TestFormType::class);
        \$form->submit(\$request->request->get(\$form->getName()));
        \$form->handleRequest(\$request);

        \$request = new UpdateTestRequest(\$id, \$form->getData(), \$this->getUser()->key());
T
, $render->build());
    }

    public function testItRenderDeleteOk(): void
    {
        $render = new RequestMessageBuilder(new Schema('Test', 'bar', []), 'delete');

        $this->assertEquals(<<<T
        \$request = new DeleteTestRequest(\$id);
T
, $render->build());
    }

    /**
     * @dataProvider getCustomRequestMessages
     *
     * @param string $action
     * @param mixed $expected
     */
    public function testItRenderCustomRequestMessageOk($action, $expected): void
    {
        $entity = 'FooBar';

        $render = new RequestMessageBuilder(new Schema($entity, 'bar', []), $action);

        $this->assertEquals(<<<T
        \$request = new {$expected}FooBarRequest(\$request->request->all());
T
, $render->build());
    }

    public function testItRenderToString(): void
    {
        $render = new RequestMessageBuilder(new Schema('Test', 'bar', []), 'index');

        $this->assertEquals(<<<T
        \$request = new IndexTestRequest(\$request->request->all());
T
, $render);
    }

    public function getCustomRequestMessages(): array
    {
        return [
            ['custom_action', 'CustomAction'],
            ['custom action', 'CustomAction'],
            ['Custom RequestMessage', 'CustomRequestmessage'],
            ['cUSTOM aCtion', 'CustomAction'],
            ['customRequestMessage', 'CustomRequestMessage'],
        ];
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
