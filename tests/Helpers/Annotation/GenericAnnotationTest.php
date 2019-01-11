<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use SlevomatCodingStandard\Helpers\TestCase;

class GenericAnnotationTest extends TestCase
{

	public function testAnnotation(): void
	{
		$annotation = new GenericAnnotation('@Something', 1, 10, 'Anything', 'Whatever');

		self::assertSame('@Something', $annotation->getName());
		self::assertSame(1, $annotation->getStartPointer());
		self::assertSame(10, $annotation->getEndPointer());
		self::assertSame('Anything', $annotation->getParameters());
		self::assertSame('Whatever', $annotation->getContent());

		self::assertFalse($annotation->isInvalid());
		self::assertSame('@Something(Anything) Whatever', $annotation->export());
	}

}
