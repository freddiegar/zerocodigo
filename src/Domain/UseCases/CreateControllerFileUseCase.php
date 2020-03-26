<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Generator\Domain\UseCases;

use FlexPHP\Generator\Domain\Builders\Controller\ActionBuilder;
use FlexPHP\Generator\Domain\Builders\Controller\ControllerBuilder;
use FlexPHP\Generator\Domain\Builders\Controller\RequestMessageBuilder;
use FlexPHP\Generator\Domain\Builders\Controller\ResponseMessageBuilder;
use FlexPHP\Generator\Domain\Builders\Controller\UseCaseBuilder;
use FlexPHP\Generator\Domain\Messages\Requests\MakeControllerRequest;
use FlexPHP\Generator\Domain\Messages\Responses\MakeControllerResponse;
use FlexPHP\UseCases\UseCase;

class CreateControllerFileUseCase extends UseCase
{
    /**
     * Create controller based in actions
     *
     * @param MakeControllerRequest $request
     *
     * @return MakeControllerResponse
     */
    public function execute($request)
    {
        $this->throwExceptionIfRequestNotValid(__METHOD__, MakeControllerRequest::class, $request);

        $entity = $request->entity;
        $actions = $request->actions;
        $actionBuilders = [];

        foreach ($actions as $action) {
            $actionBuilders[$action] = (new ActionBuilder(
                $entity,
                $action,
                (new RequestMessageBuilder($entity, $action))->build(),
                (new UseCaseBuilder($entity, $action))->build(),
                (new ResponseMessageBuilder($entity, $action))->build()
            ))->build();
        }

        $controller = new ControllerBuilder($entity, $actionBuilders);

        $dir = \sprintf('%1$s/../../tmp/skeleton/src/Controllers', __DIR__);

        if (!\is_dir($dir)) {
            \mkdir($dir, 0777, true); // @codeCoverageIgnore
        }

        $file = \sprintf('%1$s/%2$sController.php', $dir, $entity);

        \file_put_contents($file, $controller->build());

        return new MakeControllerResponse($file);
    }
}