<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\PropertyTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\TestCase;

class PropertyAnnotationTest extends TestCase
{

	public function testAnnotation(): void
	{
		$annotation = new PropertyAnnotation(
			'@property',
			1,
			10,
			'Description',
			new PropertyTagValueNode(new IdentifierTypeNode('string'), '$property', 'Description')
		);

		self::assertSame('@property', $annotation->getName());
		self::assertSame(1, $annotation->getStartPointer());
		self::assertSame(10, $annotation->getEndPointer());
		self::assertSame('Description', $annotation->getContent());

		self::assertFalse($annotation->isInvalid());
		self::assertTrue($annotation->hasDescription());
		self::assertSame('Description', $annotation->getDescription());
		self::assertSame('$property', $annotation->getPropertyName());
		self::assertInstanceOf(IdentifierTypeNode::class, $annotation->getType());
		self::assertSame('@property string $property Description', $annotation->export());
	}

	public function testUnsupportedAnnotation(): void
	{
		self::expectException(InvalidArgumentException::class);
		self::expectExceptionMessage('Unsupported annotation @var.');
		new PropertyAnnotation('@var', 1, 1, null, null);
	}

	public function testGetContentNodeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @property annotation.');
		$annotation = new PropertyAnnotation('@property', 1, 1, null, null);
		$annotation->getContentNode();
	}

	public function testGetDescriptionWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @property annotation.');
		$annotation = new PropertyAnnotation('@property', 1, 1, null, null);
		$annotation->getDescription();
	}

	public function testGetPropertyNameWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @property-read annotation.');
		$annotation = new PropertyAnnotation('@property-read', 1, 1, null, null);
		$annotation->getPropertyName();
	}

	public function testGetTypeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @property-write annotation.');
		$annotation = new PropertyAnnotation('@property-write', 1, 1, null, null);
		$annotation->getType();
	}

}
