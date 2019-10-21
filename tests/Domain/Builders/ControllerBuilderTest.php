<?php

namespace FlexPHP\Generator\Tests\Domain\Builders;

use FlexPHP\Generator\Domain\Builders\ActionBuilder;
use FlexPHP\Generator\Domain\Builders\ControllerBuilder;
use FlexPHP\Generator\Domain\Builders\RequestMessageBuilder;
use FlexPHP\Generator\Tests\TestCase;

class ControllerBuilderTest extends TestCase
{
    public function testItRenderIndexOk()
    {
        $entity = 'Test';
        $indexAction = 'index';
        $actions = [
            $indexAction => (new ActionBuilder([
                'action' => $indexAction,
                'entity' => $entity,
                'request_message' => (new RequestMessageBuilder([
                    'action' => $indexAction,
                    'entity' => $entity,
                ]))->build(),
            ]))->build(),
        ];

        $render = new ControllerBuilder([
            'entity' => $entity,
            'actions' => $actions,
        ]);

        $this->assertEquals(str_replace("\r\n","\n", <<<'T'
<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage Test.
 *
 * @Route("/test")
 *
 * @author FlexPHP <flexphp@outlook.com>
 */
class TestController extends AbstractController
{
    /**
     * @Route("/"}, methods={"GET"}, name="test.index")
     * @Cache(smaxage="10")
     */
    public function index(Request $request): Response
    {
        $requestMessage = new IndexTestRequest($request->request->all());




    }

}

T), $render->build());
    }
}
