<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\TestCase;

class ReturnAnnotationTest extends TestCase
{

	public function testAnnotation(): void
	{
		$annotation = new ReturnAnnotation(
			'@return',
			1,
			10,
			'Description',
			new ReturnTagValueNode(new IdentifierTypeNode('string'), 'Description')
		);

		self::assertSame('@return', $annotation->getName());
		self::assertSame(1, $annotation->getStartPointer());
		self::assertSame(10, $annotation->getEndPointer());
		self::assertSame('Description', $annotation->getContent());

		self::assertFalse($annotation->isInvalid());
		self::assertTrue($annotation->hasDescription());
		self::assertSame('Description', $annotation->getDescription());
		self::assertInstanceOf(IdentifierTypeNode::class, $annotation->getType());
		self::assertSame('@return string Description', $annotation->export());
	}

	public function testUnsupportedAnnotation(): void
	{
		self::expectException(InvalidArgumentException::class);
		self::expectExceptionMessage('Unsupported annotation @param.');
		new ReturnAnnotation('@param', 1, 1, null, null);
	}

	public function testGetContentNodeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @return annotation.');
		$annotation = new ReturnAnnotation('@return', 1, 1, null, null);
		$annotation->getContentNode();
	}

	public function testGetDescriptionWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @return annotation.');
		$annotation = new ReturnAnnotation('@return', 1, 1, null, null);
		$annotation->getDescription();
	}

	public function testGetTypeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @return annotation.');
		$annotation = new ReturnAnnotation('@return', 1, 1, null, null);
		$annotation->getType();
	}

}
