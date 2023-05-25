<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\SelfOutTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\TestCase;

class SelfOutAnnotationTest extends TestCase
{

	public function testAnnotation(): void
	{
		$annotation = new SelfOutAnnotation(
			'@phpstan-self-out',
			1,
			10,
			'Description',
			new SelfOutTagValueNode(new IdentifierTypeNode('string'), 'Description')
		);

		self::assertSame('@phpstan-self-out', $annotation->getName());
		self::assertSame(1, $annotation->getStartPointer());
		self::assertSame(10, $annotation->getEndPointer());
		self::assertSame('Description', $annotation->getContent());

		self::assertFalse($annotation->isInvalid());
		self::assertTrue($annotation->hasDescription());
		self::assertSame('Description', $annotation->getDescription());
		self::assertInstanceOf(IdentifierTypeNode::class, $annotation->getType());
		self::assertSame('@phpstan-self-out string Description', $annotation->print());
	}

	public function testUnsupportedAnnotation(): void
	{
		self::expectException(InvalidArgumentException::class);
		self::expectExceptionMessage('Unsupported annotation @var.');
		new SelfOutAnnotation('@var', 1, 1, null, null);
	}

	public function testGetContentNodeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @phpstan-self-out annotation.');
		$annotation = new SelfOutAnnotation('@phpstan-self-out', 1, 1, null, null);
		$annotation->getContentNode();
	}

	public function testGetDescriptionWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @phpstan-self-out annotation.');
		$annotation = new SelfOutAnnotation('@phpstan-self-out', 1, 1, null, null);
		$annotation->getDescription();
	}

	public function testGetTypeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @phpstan-self-out annotation.');
		$annotation = new SelfOutAnnotation('@phpstan-self-out', 1, 1, null, null);
		$annotation->getType();
	}

}
