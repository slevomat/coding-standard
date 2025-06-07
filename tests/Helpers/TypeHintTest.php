<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class TypeHintTest extends TestCase
{

	public function test(): void
	{
		$typeHint = new TypeHint('?string', true, 1, 6);

		self::assertSame('?string', $typeHint->getTypeHint());
		self::assertTrue($typeHint->isNullable());
		self::assertSame(1, $typeHint->getStartPointer());
		self::assertSame(6, $typeHint->getEndPointer());
	}

}
