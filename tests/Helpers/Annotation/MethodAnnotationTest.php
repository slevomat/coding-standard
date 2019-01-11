<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueParameterNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\TestCase;

class MethodAnnotationTest extends TestCase
{

	public function testAnnotation(): void
	{
		$annotation = new MethodAnnotation(
			'@method',
			1,
			10,
			'string method(int $p) Description',
			new MethodTagValueNode(
				false,
				new IdentifierTypeNode('string'),
				'method',
				[new MethodTagValueParameterNode(new IdentifierTypeNode('int'), false, false, '$p', null)],
				'Description'
			)
		);

		self::assertSame('@method', $annotation->getName());
		self::assertSame(1, $annotation->getStartPointer());
		self::assertSame(10, $annotation->getEndPointer());
		self::assertSame('string method(int $p) Description', $annotation->getContent());

		self::assertFalse($annotation->isInvalid());
		self::assertTrue($annotation->hasDescription());
		self::assertSame('Description', $annotation->getDescription());
		self::assertSame('method', $annotation->getMethodName());
		self::assertInstanceOf(IdentifierTypeNode::class, $annotation->getMethodReturnType());
		self::assertCount(1, $annotation->getMethodParameters());
		self::assertSame('@method string method(int $p) Description', $annotation->export());
	}

	public function testUnsupportedAnnotation(): void
	{
		self::expectException(InvalidArgumentException::class);
		self::expectExceptionMessage('Unsupported annotation @var.');
		new MethodAnnotation('@var', 1, 1, null, null);
	}

	public function testGetContentNodeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @method annotation.');
		$annotation = new MethodAnnotation('@method', 1, 1, null, null);
		$annotation->getContentNode();
	}

	public function testGetDescriptionWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @method annotation.');
		$annotation = new MethodAnnotation('@method', 1, 1, null, null);
		$annotation->getDescription();
	}

	public function testGetMethodNameWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @method annotation.');
		$annotation = new MethodAnnotation('@method', 1, 1, null, null);
		$annotation->getMethodName();
	}

	public function testGetMethodReturnTypeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @method annotation.');
		$annotation = new MethodAnnotation('@method', 1, 1, null, null);
		$annotation->getMethodReturnType();
	}

	public function testGetMethodParametersWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @method annotation.');
		$annotation = new MethodAnnotation('@method', 1, 1, null, null);
		$annotation->getMethodParameters();
	}

}
