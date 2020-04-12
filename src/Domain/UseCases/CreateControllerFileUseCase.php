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
use FlexPHP\Generator\Domain\Messages\Requests\CreateControllerFileRequest;
use FlexPHP\Generator\Domain\Messages\Responses\CreateControllerFileResponse;
use FlexPHP\Generator\Domain\Traits\InflectorTrait;
use FlexPHP\Generator\Domain\Writers\PhpWriter;

final class CreateControllerFileUseCase
{
    use InflectorTrait;

    public function execute(CreateControllerFileRequest $request): CreateControllerFileResponse
    {
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
        $filename = $this->getSingularize($entity) . 'Controller';
        $path = \sprintf('%1$s/../../tmp/skeleton/src/Controller', __DIR__);

        $writer = new PhpWriter($controller->build(), $filename, $path);

        return new CreateControllerFileResponse($writer->save());
    }
}
