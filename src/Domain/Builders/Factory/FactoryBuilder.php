<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Generator\Domain\Builders\Factory;

use FlexPHP\Generator\Domain\Builders\AbstractBuilder;

final class FactoryBuilder extends AbstractBuilder
{
    public function __construct(string $entity)
    {
        $entity = $this->getPascalCase($this->getSingularize($entity));
        $item = $this->getCamelCase($this->getSingularize($entity));

        parent::__construct(\compact('entity', 'item'));
    }

    protected function getFileTemplate(): string
    {
        return 'Factory.php.twig';
    }

    protected function getPathTemplate(): string
    {
        return \sprintf('%1$s/FlexPHP/Factory', parent::getPathTemplate());
    }
}
