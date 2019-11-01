<?php

namespace FlexPHP\Generator\Tests\Domain\Validations;

use FlexPHP\Generator\Domain\Constants\Keyword;
use FlexPHP\Generator\Domain\Exceptions\FieldSyntaxValidationException;
use FlexPHP\Generator\Domain\Validations\FieldSyntaxValidation;
use FlexPHP\Generator\Domain\Validators\PropertyConstraintsValidator;
use FlexPHP\Generator\Domain\Validators\PropertyDataTypeValidator;
use FlexPHP\Generator\Tests\TestCase;

class FieldSyntaxValidationTest extends TestCase
{
    public function testItPropertyUnknowThrownException(): void
    {
        $this->expectException(FieldSyntaxValidationException::class);
        $this->expectExceptionMessage('unknow');

        $validation = new FieldSyntaxValidation([
            'UnknowProperty' => 'Test',
        ]);

        $validation->validate();
    }

    /**
     * @dataProvider propertyNameNotValid
     */
    public function testItPropertyNameNotValidThrownException($name): void
    {
        $this->expectException(FieldSyntaxValidationException::class);
        $this->expectExceptionMessage('Name:');

        $validation = new FieldSyntaxValidation([
            Keyword::NAME => $name,
        ]);

        $validation->validate();
    }

    /**
     * @dataProvider propertyNameValid
     */
    public function testItPropertyNameOk($name): void
    {
        $validation = new FieldSyntaxValidation([
            Keyword::NAME => $name,
        ]);

        $validation->validate();

        $this->assertTrue(true);
    }

    /**
     * @dataProvider propertyDataTypeNotValid
     */
    public function testItPropertyDataTypeNotValidThrownException($dataType): void
    {
        $this->expectException(FieldSyntaxValidationException::class);
        $this->expectExceptionMessage('DataType:');

        $validation = new FieldSyntaxValidation([
            Keyword::DATA_TYPE => $dataType,
        ]);

        $validation->validate();
    }

    /**
     * @dataProvider propertyDataTypeValid
     */
    public function testItPropertyDataTypeOk($dataType): void
    {
        $validation = new FieldSyntaxValidation([
            Keyword::DATA_TYPE => $dataType,
        ]);

        $validation->validate();

        $this->assertTrue(true);
    }

    /**
     * @dataProvider propertyConstraintsNotValid
     */
    public function testItPropertyConstraintsNotValidThrownException($constraints): void
    {
        $this->expectException(FieldSyntaxValidationException::class);
        $this->expectExceptionMessage('Constraints:');

        $validation = new FieldSyntaxValidation([
            Keyword::CONSTRAINTS => $constraints,
        ]);

        $validation->validate();
    }

    /**
     * @dataProvider propertyConstraintsValid
     */
    public function testItPropertyConstraintsOk($constraints): void
    {
        $validation = new FieldSyntaxValidation([
            Keyword::CONSTRAINTS => $constraints,
        ]);

        $validation->validate();

        $this->assertTrue(true);
    }

    public function propertyNameNotValid(): array
    {
        return [
            ['#Name'],
            ['1Name'],
            ['Name$'],
            [str_repeat('N', 65)],
            [''],
        ];
    }

    public function propertyNameValid(): array
    {
        return [
            ['Name'],
            ['N123'],
            ['Name_Test'],
            ['name_test'],
            ['_name'],
            [str_repeat('N', 64)],
            ['N'],
        ];
    }

    public function propertyDataTypeNotValid(): array
    {
        return [
            ['unknow'],
            ['bool'],
            ['barchar'],
            ['interger'],
            ['int'],
            [null],
            [[]],
            [1],
        ];
    }

    public function propertyDataTypeValid(): array
    {
        return array_map(function ($dataType) {
            return [$dataType];
        }, PropertyDataTypeValidator::ALLOWED_DATA_TYPES);
    }

    public function propertyConstraintsNotValid(): array
    {
        return [
            ['_REQUIRED'],
            ['REQUIRED'],
            ['Required'],
            [null],
            [1],
            [['minlength' => null]],
            [['maxlength' => []]],
            [['mincheck' => -1]],
            [['maxcheck' => 0]],
            [['min' => '']],
            [['max' => 'null']],
            [['equalto' => null]],
            [['type' => 'unknow']],
            [['check' => [
                'min' => rand(5, 10),
            ]]],
            [['check' => [
                'min' => rand(5, 10),
                'max' => rand(0, 4),
            ]]],
            [['length' => [
                'max' => rand(0, 5),
            ]]],
            [['length' => [
                'min' => rand(5, 10),
                'max' => rand(0, 5),
            ]]],
        ];
    }

    public function propertyConstraintsValid(): array
    {
        return [
            [['required']],
            [['required' => true]],
            [['required' => false]],
            [[]],
            [['minlength' => 0]],
            [['minlength' => rand(0, 9)]],
            [['maxlength' => rand(1, 9)]],
            [['mincheck' => 0]],
            [['mincheck' => rand(1, 9)]],
            [['maxcheck' => rand(1, 9)]],
            [['min' => 0]],
            [['min' => rand(1, 9)]],
            [['max' => rand(1, 9)]],
            [['equalto' => 'foo']],
            [['type' => 'text']],
            [['check' => [
                'min' => rand(0, 4),
                'max' => rand(5, 10),
            ]]],
            [['length' => [
                'min' => rand(0, 4),
                'max' => rand(5, 10),
            ]]],
        ];
    }
}
