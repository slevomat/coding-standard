<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\TypeAliasTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\TestCase;

class TypeAliasAnnotationTest extends TestCase
{

	public function testAnnotation(): void
	{
		$annotation = new TypeAliasAnnotation(
			'@phpstan-type',
			1,
			10,
			null,
			new TypeAliasTagValueNode('Whatever', new IdentifierTypeNode('Anything'))
		);

		self::assertSame('@phpstan-type', $annotation->getName());
		self::assertSame(1, $annotation->getStartPointer());
		self::assertSame(10, $annotation->getEndPointer());
		self::assertNull($annotation->getContent());

		self::assertFalse($annotation->isInvalid());
		self::assertSame('Whatever', $annotation->getAlias());
		self::assertInstanceOf(IdentifierTypeNode::class, $annotation->getType());
		self::assertSame('@phpstan-type Whatever Anything', $annotation->export());
	}

	public function testUnsupportedAnnotation(): void
	{
		self::expectException(InvalidArgumentException::class);
		self::expectExceptionMessage('Unsupported annotation @param.');
		new TypeAliasAnnotation('@param', 1, 1, null, null);
	}

	public function testGetContentNodeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @phpstan-type annotation.');
		$annotation = new TypeAliasAnnotation('@phpstan-type', 1, 1, null, null);
		$annotation->getContentNode();
	}

	public function testGetAliasWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @phpstan-type annotation.');
		$annotation = new TypeAliasAnnotation('@phpstan-type', 1, 1, null, null);
		$annotation->getAlias();
	}

	public function testGetTypeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @phpstan-type annotation.');
		$annotation = new TypeAliasAnnotation('@phpstan-type', 1, 1, null, null);
		$annotation->getType();
	}

}
