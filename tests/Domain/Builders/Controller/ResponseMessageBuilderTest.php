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

use FlexPHP\Generator\Domain\Builders\Controller\ResponseMessageBuilder;
use FlexPHP\Generator\Tests\TestCase;
use FlexPHP\Schema\Schema;

final class ResponseMessageBuilderTest extends TestCase
{
    public function testItRenderIndexOk(): void
    {
        $render = new ResponseMessageBuilder(new Schema('Test', 'bar', []), 'index');

        $this->assertEquals(<<<T
        return \$this->render('test/index.html.twig', [
            'registers' => \$response->tests,
        ]);
T
, $render->build());
    }

    public function testItRenderCreateOk(): void
    {
        $render = new ResponseMessageBuilder(new Schema('Test', 'bar', []), 'create');

        $this->assertEquals(<<<T
        \$this->addFlash(\$response->status, \$response->message);

        return \$this->redirectToRoute('tests.index');
T
, $render->build());
    }

    public function testItRenderReadOk(): void
    {
        $render = new ResponseMessageBuilder(new Schema('Test', 'bar', []), 'read');

        $this->assertEquals(<<<T
        return \$this->render('test/show.html.twig', [
            'register' => \$response->test,
        ]);
T
, $render->build());
    }

    public function testItRenderUpdateOk(): void
    {
        $render = new ResponseMessageBuilder(new Schema('Test', 'bar', []), 'update');

        $this->assertEquals(<<<T
        \$this->addFlash(\$response->status, \$response->message);

        return \$this->redirectToRoute('tests.index');
T
, $render->build());
    }

    public function testItRenderDeleteOk(): void
    {
        $render = new ResponseMessageBuilder(new Schema('Test', 'bar', []), 'delete');

        $this->assertEquals(<<<T
        \$this->addFlash(\$response->status, \$response->message);

        return \$this->redirectToRoute('tests.index');
T
, $render->build());
    }

    public function testItRenderWithActionOk(): void
    {
        $render = new ResponseMessageBuilder(new Schema('Test', 'bar', []), 'action');

        $this->assertEquals(<<<T
        return new Response(\$response);
T
, $render->build());
    }
}
