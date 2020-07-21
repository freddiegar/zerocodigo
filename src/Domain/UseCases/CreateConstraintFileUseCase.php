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

use FlexPHP\Generator\Domain\Builders\Constraint\ConstraintBuilder;
use FlexPHP\Generator\Domain\Builders\Constraint\RuleBuilder;
use FlexPHP\Generator\Domain\Messages\Requests\CreateConstraintFileRequest;
use FlexPHP\Generator\Domain\Messages\Responses\CreateConstraintFileResponse;
use FlexPHP\Generator\Domain\Traits\InflectorTrait;
use FlexPHP\Generator\Domain\Writers\PhpWriter;
use FlexPHP\Schema\SchemaAttributeInterface;

final class CreateConstraintFileUseCase
{
    use InflectorTrait;

    public function execute(CreateConstraintFileRequest $request): CreateConstraintFileResponse
    {
        $entity = $this->getPascalCase($this->getSingularize($request->schema->name()));
        $properties = \array_reduce(
            $request->schema->attributes(),
            function (array $result, SchemaAttributeInterface $schemaAttribute) {
                $name = $schemaAttribute->name();
                $result[$name] = (new RuleBuilder($name, $schemaAttribute->constraints()))->build();

                return $result;
            },
            []
        );

        $constraint = new ConstraintBuilder($entity, $properties);
        $filename = $entity . 'Constraint';
        $path = \sprintf('%1$s/../../tmp/skeleton/domain/%2$s', __DIR__, $entity);

        $writer = new PhpWriter($constraint->build(), $filename, $path);

        return new CreateConstraintFileResponse($writer->save());
    }
}
