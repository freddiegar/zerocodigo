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

use FlexPHP\Generator\Domain\Builders\FormType\FormTypeBuilder;
use FlexPHP\Generator\Domain\Builders\Inflector;
use FlexPHP\Generator\Domain\Messages\Requests\CreateFormTypeFileRequest;
use FlexPHP\Generator\Domain\Messages\Responses\CreateFormTypeFileResponse;
use FlexPHP\Generator\Domain\Writers\PhpWriter;

final class CreateFormTypeFileUseCase
{
    public function execute(CreateFormTypeFileRequest $request): CreateFormTypeFileResponse
    {
        $inflector = new Inflector();
        $entity = $inflector->entity($request->schema->name());

        $formType = new FormTypeBuilder($request->schema);
        $filename = $entity . 'FormType';
        $path = \sprintf('%1$s/../../tmp/skeleton/domain/%2$s', __DIR__, $entity);

        $writer = new PhpWriter($formType->build(), $filename, $path);

        return new CreateFormTypeFileResponse($writer->save());
    }
}
