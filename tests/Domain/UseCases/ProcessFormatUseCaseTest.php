<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Generator\Tests\Domain\UseCases;

use FlexPHP\Generator\Domain\Exceptions\FormatNotSupportedException;
use FlexPHP\Generator\Domain\Exceptions\FormatPathNotValidException;
use FlexPHP\Generator\Domain\Messages\Requests\ProcessFormatRequest;
use FlexPHP\Generator\Domain\Messages\Responses\ProcessFormatResponse;
use FlexPHP\Generator\Domain\UseCases\ProcessFormatUseCase;
use FlexPHP\Generator\Tests\TestCase;

final class ProcessFormatUseCaseTest extends TestCase
{
    /**
     * @dataProvider getPathNotValid
     *
     * @param mixed $path
     */
    public function testItFormatPathNotValidThrowException($path): void
    {
        $this->expectException(FormatPathNotValidException::class);

        $request = new ProcessFormatRequest($path, 'xlsx');

        $useCase = new ProcessFormatUseCase();
        $useCase->execute($request);
    }

    public function getPathNotValid(): array
    {
        return [
            [''],
            ['/path/not/exist'],
        ];
    }

    public function testItFormatNotSupportedThrowException(): void
    {
        $this->expectException(FormatNotSupportedException::class);

        $request = new ProcessFormatRequest(\sprintf('%1$s/../../../src/dist/templates/Format.xlsx', __DIR__), 'doc');

        $useCase = new ProcessFormatUseCase();
        $useCase->execute($request);
    }

    public function testItFormatOk(): void
    {
        $request = new ProcessFormatRequest(\sprintf('%1$s/../../../src/dist/templates/Format.xlsx', __DIR__), 'xlsx');

        $useCase = new ProcessFormatUseCase();
        $response = $useCase->execute($request);

        $this->assertInstanceOf(ProcessFormatResponse::class, $response);
        $sheetNames = $response->messages;
        $this->assertEquals(2, \count($sheetNames));

        foreach ($sheetNames as $sheetName => $numberFields) {
            $numberExpected = 0;
            $contentExpected = '';

            switch ($sheetName) {
                case 'Posts':
                    $numberExpected = 6;
                    $contentExpected = $this->getContentPostFile();

                    break;
                case 'Comments':
                    $numberExpected = 5;
                    $contentExpected = $this->getContentCommentFile();

                    break;
                default:
                    $this->assertTrue(false, 'SheetName unknown: ' . $sheetName);

                    break;
            }

            $this->assertEquals($numberExpected, $numberFields);

            $yaml = \sprintf('%1$s/../../../src/tmp/%2$s.yaml', __DIR__, \strtolower($sheetName));

            $this->assertFileExists($yaml);
            $this->assertEquals($contentExpected, \file_get_contents($yaml));

            \unlink($yaml);
        }
    }

    private function getContentPostFile(): string
    {
        return <<<T
Posts:
  Title: Posts
  Icon: 'fas fa-mail'
  Attributes:
    id:
      Name: Id
      DataType: integer
      Constraints: 'pk|ai|required|type:number'
    title:
      Name: Title
      DataType: string
      Constraints: 'required|maxlength:255'
    content:
      Name: Content
      DataType: text
      Constraints: 'required|type:textarea|length:10,512'
    isActive:
      Name: IsActive
      DataType: boolean
      Constraints: ''
    createdAt:
      Name: CreatedAt
      DataType: datetime
      Constraints: ca
    updatedAt:
      Name: UpdatedAt
      DataType: datetime
      Constraints: ua

T;
    }

    private function getContentCommentFile(): string
    {
        return <<<T
Comments:
  Title: Comments
  Icon: ''
  Attributes:
    id:
      Name: Id
      DataType: integer
      Constraints: pk|ai|required
    comment:
      Name: Comment
      DataType: text
      Constraints: 'required|min:3|max:256'
    postId:
      Name: PostId
      DataType: integer
      Constraints: 'required|fk:posts,title'
    createdAt:
      Name: CreatedAt
      DataType: datetime
      Constraints: ca
    updatedAt:
      Name: UpdatedAt
      DataType: datetime
      Constraints: ua

T;
    }
}
