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
use FlexPHP\Generator\Domain\Builders\Translate\TranslateBuilder;
use FlexPHP\Generator\Domain\Messages\Requests\CreateTranslateFileRequest;
use FlexPHP\Generator\Domain\Messages\Responses\CreateTranslateFileResponse;
use FlexPHP\Generator\Domain\Writers\PhpWriter;

final class CreateTranslateFileUseCase
{
    public function execute(CreateTranslateFileRequest $request): CreateTranslateFileResponse
    {
        $inflector = new Inflector();
        $file = $inflector->item($request->schema->name());

        $translate = new TranslateBuilder($request->schema);
        $path = \sprintf('%1$s/../../tmp/skeleton/translations', __DIR__);
        $filename = \sprintf('%1$s.%2$s', $file, $request->schema->language());

        $writer = new PhpWriter($translate->build(), $filename, $path);

        return new CreateTranslateFileResponse($writer->save());
    }
}
