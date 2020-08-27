<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\TestCase;

class VariableAnnotationTest extends TestCase
{

	public function testAnnotation(): void
	{
		$annotation = new VariableAnnotation(
			'@var',
			1,
			10,
			'Description',
			new VarTagValueNode(new IdentifierTypeNode('string'), '$variable', 'Description')
		);

		self::assertSame('@var', $annotation->getName());
		self::assertSame(1, $annotation->getStartPointer());
		self::assertSame(10, $annotation->getEndPointer());
		self::assertSame('Description', $annotation->getContent());

		self::assertFalse($annotation->isInvalid());
		self::assertTrue($annotation->hasDescription());
		self::assertSame('Description', $annotation->getDescription());
		self::assertSame('$variable', $annotation->getVariableName());
		self::assertInstanceOf(IdentifierTypeNode::class, $annotation->getType());
		self::assertSame('@var string $variable Description', $annotation->export());
	}

	public function testUnsupportedAnnotation(): void
	{
		self::expectException(InvalidArgumentException::class);
		self::expectExceptionMessage('Unsupported annotation @param.');
		new VariableAnnotation('@param', 1, 1, null, null);
	}

	public function testGetContentNodeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @var annotation.');
		$annotation = new VariableAnnotation('@var', 1, 1, null, null);
		$annotation->getContentNode();
	}

	public function testGetDescriptionWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @var annotation.');
		$annotation = new VariableAnnotation('@var', 1, 1, null, null);
		$annotation->getDescription();
	}

	public function testGetVariableNameWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @var annotation.');
		$annotation = new VariableAnnotation('@var', 1, 1, null, null);
		$annotation->getVariableName();
	}

	public function testGetTypeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @var annotation.');
		$annotation = new VariableAnnotation('@var', 1, 1, null, null);
		$annotation->getType();
	}

}
