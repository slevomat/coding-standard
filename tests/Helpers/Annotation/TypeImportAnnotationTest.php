<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\TypeAliasImportTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\TestCase;

class TypeImportAnnotationTest extends TestCase
{

	public function testAnnotation(): void
	{
		$annotation = new TypeImportAnnotation(
			'@phpstan-import-type',
			1,
			10,
			null,
			new TypeAliasImportTagValueNode('Whatever', new IdentifierTypeNode('Anything'), 'ImportedAs')
		);

		self::assertSame('@phpstan-import-type', $annotation->getName());
		self::assertSame(1, $annotation->getStartPointer());
		self::assertSame(10, $annotation->getEndPointer());
		self::assertNull($annotation->getContent());

		self::assertFalse($annotation->isInvalid());
		self::assertSame('Whatever', $annotation->getImportedAlias());
		self::assertSame('ImportedAs', $annotation->getImportedAs());
		self::assertSame('@phpstan-import-type Whatever from Anything as ImportedAs', $annotation->export());
	}

	public function testUnsupportedAnnotation(): void
	{
		self::expectException(InvalidArgumentException::class);
		self::expectExceptionMessage('Unsupported annotation @param.');
		new TypeImportAnnotation('@param', 1, 1, null, null);
	}

	public function testGetContentNodeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @phpstan-import-type annotation.');
		$annotation = new TypeImportAnnotation('@phpstan-import-type', 1, 1, null, null);
		$annotation->getContentNode();
	}

	public function testGetImportedAliasWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @phpstan-import-type annotation.');
		$annotation = new TypeImportAnnotation('@phpstan-import-type', 1, 1, null, null);
		$annotation->getImportedAlias();
	}

	public function testGetImportedFromWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @phpstan-import-type annotation.');
		$annotation = new TypeImportAnnotation('@phpstan-import-type', 1, 1, null, null);
		$annotation->getImportedFrom();
	}

	public function testGetImportedAsWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @phpstan-import-type annotation.');
		$annotation = new TypeImportAnnotation('@phpstan-import-type', 1, 1, null, null);
		$annotation->getImportedAs();
	}

}
