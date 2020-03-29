<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Generator\Domain\Builders\Controller;

use FlexPHP\Generator\Domain\Builders\AbstractBuilder;

final class UseCaseBuilder extends AbstractBuilder
{
    public function __construct(string $entity, string $action)
    {
        $entity = $this->getPascalCase($this->getSingularize($entity));
        $action = $this->getPascalCase($action);

        parent::__construct(\compact('entity', 'action'));
    }

    protected function getFileTemplate(): string
    {
        return 'UseCase.php.twig';
    }

    protected function getPathTemplate(): string
    {
        return \sprintf('%1$s/Symfony/v43/src/Controller', parent::getPathTemplate());
    }
}
