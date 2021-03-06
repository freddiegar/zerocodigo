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

use FlexPHP\Generator\Domain\Builders\Inflector;
use FlexPHP\Generator\Domain\Builders\UseCase\FkUseCaseBuilder;
use FlexPHP\Generator\Domain\Builders\UseCase\UseCaseBuilder;
use FlexPHP\Generator\Domain\Messages\Requests\CreateUseCaseFileRequest;
use FlexPHP\Generator\Domain\Messages\Responses\CreateUseCaseFileResponse;
use FlexPHP\Generator\Domain\Writers\PhpWriter;

final class CreateUseCaseFileUseCase
{
    public function execute(CreateUseCaseFileRequest $request): CreateUseCaseFileResponse
    {
        $files = [];
        $inflector = new Inflector();
        $entity = $inflector->entity($request->schema->name());
        $path = \sprintf('%1$s/../../tmp/skeleton/domain/%2$s/UseCase', __DIR__, $entity);

        foreach ($request->actions as $action) {
            $useCase = new UseCaseBuilder($request->schema, $action);
            $filename = $inflector->pascalAction($action) . $entity . 'UseCase';

            $writer = new PhpWriter($useCase->build(), $filename, $path);
            $files[] = $writer->save();
        }

        foreach ($request->schema->fkRelations() as $fkRel) {
            $fkEntity = $inflector->entity($fkRel['pkTable']);
            $builder = new FkUseCaseBuilder($request->schema->name(), $fkEntity);
            $filename = 'Find' . $entity . $fkEntity . 'UseCase';

            $writer = new PhpWriter($builder->build(), $filename, $path);
            $files[] = $writer->save();
        }

        return new CreateUseCaseFileResponse($files);
    }
}
