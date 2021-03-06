<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Generator\Domain\Messages\Requests;

use FlexPHP\Schema\SchemaInterface;

final class CreateUseCaseFileRequest
{
    /**
     * @var SchemaInterface
     */
    public $schema;

    /**
     * @var array
     */
    public $actions;

    public function __construct(SchemaInterface $schema, array $actions)
    {
        $this->schema = $schema;
        $this->actions = $actions;
    }
}
