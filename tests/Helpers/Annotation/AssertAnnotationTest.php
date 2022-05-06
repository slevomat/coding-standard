<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\AssertTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\TestCase;

class AssertAnnotationTest extends TestCase
{

	public function testAnnotation(): void
	{
		$annotation = new AssertAnnotation(
			'@phpstan-assert',
			1,
			10,
			'Description',
			new AssertTagValueNode(
				new IdentifierTypeNode('string'),
				'$parameter',
				false,
				'Description'
			)
		);

		self::assertSame('@phpstan-assert', $annotation->getName());
		self::assertSame(1, $annotation->getStartPointer());
		self::assertSame(10, $annotation->getEndPointer());
		self::assertSame('Description', $annotation->getContent());

		self::assertFalse($annotation->isInvalid());
		self::assertTrue($annotation->hasDescription());
		self::assertSame('Description', $annotation->getDescription());
		self::assertSame('@phpstan-assert string $parameter Description', $annotation->export());
	}

	public function testUnsupportedAnnotation(): void
	{
		self::expectException(InvalidArgumentException::class);
		self::expectExceptionMessage('Unsupported annotation @param.');
		new AssertAnnotation('@param', 1, 1, null, null);
	}

	public function testGetContentNodeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @phpstan-assert annotation.');
		$annotation = new AssertAnnotation('@phpstan-assert', 1, 1, null, null);
		$annotation->getContentNode();
	}

	public function testGetDescriptionWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @phpstan-assert annotation.');
		$annotation = new AssertAnnotation('@phpstan-assert', 1, 1, null, null);
		$annotation->getDescription();
	}

	public function testGetTypeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @phpstan-assert annotation.');
		$annotation = new AssertAnnotation('@phpstan-assert', 1, 1, null, null);
		$annotation->getType();
	}

}
