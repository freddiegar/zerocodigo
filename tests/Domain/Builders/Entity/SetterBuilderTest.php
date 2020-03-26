<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Generator\Tests\Domain\Builders\Entity;

use FlexPHP\Generator\Domain\Builders\Entity\SetterBuilder;
use FlexPHP\Generator\Tests\TestCase;

final class SetterBuilderTest extends TestCase
{
    public function testItWithDefaultType(): void
    {
        $render = new SetterBuilder('foo', '');

        $this->assertEquals(<<<T
    public function setFoo(string \$foo): void
    {
        \$this->foo = \$foo;
    }
T, $render->build());
    }

    /**
     * @dataProvider getDataTypeString
     *
     * @param string $dataType
     */
    public function testItWithString($dataType): void
    {
        $render = new SetterBuilder('foo', $dataType);

        $this->assertEquals(<<<T
    public function setFoo(string \$foo): void
    {
        \$this->foo = \$foo;
    }
T, $render->build());
    }

    /**
     * @dataProvider getDataTypeInt
     *
     * @param string $dataType
     */
    public function testItWithInt($dataType): void
    {
        $render = new SetterBuilder('foo', $dataType);

        $this->assertEquals(<<<T
    public function setFoo(int \$foo): void
    {
        \$this->foo = \$foo;
    }
T, $render->build());
    }

    /**
     * @dataProvider getDataTypeArray
     *
     * @param string $dataType
     */
    public function testItWithArray($dataType): void
    {
        $render = new SetterBuilder('foo', $dataType);

        $this->assertEquals(<<<T
    public function setFoo(array \$foo): void
    {
        \$this->foo = \$foo;
    }
T, $render->build());
    }

    /**
     * @dataProvider getDataTypeBool
     *
     * @param string $dataType
     */
    public function testItWithBool($dataType): void
    {
        $render = new SetterBuilder('foo', $dataType);

        $this->assertEquals(<<<T
    public function setFoo(bool \$foo): void
    {
        \$this->foo = \$foo;
    }
T, $render->build());
    }

    public function testItWithUnknowType(): void
    {
        $render = new SetterBuilder('foo', 'unknow');

        $this->assertEquals(<<<T
    public function setFoo(string \$foo): void
    {
        \$this->foo = \$foo;
    }
T, $render->build());
    }

    public function getDataTypeString(): array
    {
        return [
            ['string'],
            ['text'],
            ['text'],
            ['guid'],
            ['binary'],
            ['blob'],
            ['date'],
            ['datetime'],
            ['datetimetz'],
            ['time'],
        ];
    }

    public function getDataTypeInt(): array
    {
        return [
            ['smallint'],
            ['integer'],
            ['bigint'],
            ['decimal'],
        ];
    }

    public function getDataTypeArray(): array
    {
        return [
            ['array'],
        ];
    }

    public function getDataTypeFloat(): array
    {
        return [
            ['float'],
        ];
    }

    public function getDataTypeBool(): array
    {
        return [
            ['bool'],
            ['boolean'],
        ];
    }
}
