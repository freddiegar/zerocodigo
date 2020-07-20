<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Generator\Domain\Builders\UseCase;

use FlexPHP\Generator\Domain\Builders\AbstractBuilder;
use FlexPHP\Schema\SchemaAttributeInterface;
use FlexPHP\Schema\SchemaInterface;

final class UseCaseBuilder extends AbstractBuilder
{
    private $action;

    public function __construct(string $entity, string $action, ?SchemaInterface $schema = null)
    {
        $this->action = $this->getCamelCase($action);

        $entity = $this->getPascalCase($this->getSingularize($entity));
        $name = $this->getCamelCase($this->getSingularize($entity));
        $item = $this->getCamelCase($this->getPluralize($entity));
        $action = $this->getPascalCase($action);

        $properties = [];

        if ($schema) {
            $properties = \array_reduce($schema->attributes(), function ($result, SchemaAttributeInterface $property) {
                $result[$this->getCamelCase($property->name())] = $property->properties();

                return $result;
            }, []);
        }

        parent::__construct(\compact('entity', 'name', 'item', 'action', 'properties'));
    }

    protected function getFileTemplate(): string
    {
        if (\in_array($this->action, ['index', 'create', 'read', 'update', 'delete', 'login'])) {
            return $this->action . '.php.twig';
        }

        return 'default.php.twig';
    }

    protected function getPathTemplate(): string
    {
        return \sprintf('%1$s/FlexPHP/UseCase', parent::getPathTemplate());
    }
}
