<?php

namespace FlexPHP\Generator\Tests\Domain\Builders\UseCase;

use FlexPHP\Generator\Domain\Builders\UseCase\UseCaseBuilder;
use FlexPHP\Generator\Domain\Constants\Header;
use FlexPHP\Generator\Tests\TestCase;

class UseCaseBuilderTest extends TestCase
{
    public function testItRenderOk()
    {
        $action = 'action';
        $entity = 'Test';
        $properties = [
            'foo' => [
                Header::NAME => 'Foo',
                Header::DATA_TYPE => 'integer',
            ],
            'bar' => [
                Header::NAME => 'Bar',
                Header::DATA_TYPE => 'varchar',
            ],
        ];

        $render = new UseCaseBuilder([
            'entity' => $entity,
            'action' => $action,
            'properties' => $properties,
        ]);

        $this->assertEquals(str_replace("\r\n","\n", <<<'T'
<?php

namespace Domain\Test\UseCase;

use Domain\Test\Message\ActionTestRequest;
use Domain\Test\Message\ActionTestResponse;
use FlexPHP\UseCases\UseCase;

/**
 * UseCase to action on Test.
 *
 * @author FlexPHP <flexphp@outlook.com>
 */
class TestUseCase extends UseCase
{
    private $foo;
    private $bar;

    public function execute($request): ActionTestResponse
    {
        $this->foo = $request->foo;
        $this->bar = $request->bar;

        return ActionTestResponse();
    }
}

T), $render->build());
    }
}