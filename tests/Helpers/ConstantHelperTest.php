<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ConstantHelperTest extends TestCase
{

	public function testNameWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/constantWithNamespace.php');

		$constantPointer = $this->findConstantPointerByName($phpcsFile, 'FOO');
		self::assertSame('\FooNamespace\FOO', ConstantHelper::getFullyQualifiedName($phpcsFile, $constantPointer));
		self::assertSame('FOO', ConstantHelper::getName($phpcsFile, $constantPointer));
	}

	public function testNameWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/constantWithoutNamespace.php');

		$constantPointer = $this->findConstantPointerByName($phpcsFile, 'FOO');
		self::assertSame('FOO', ConstantHelper::getFullyQualifiedName($phpcsFile, $constantPointer));
		self::assertSame('FOO', ConstantHelper::getName($phpcsFile, $constantPointer));
	}

	public function testGetAllNames(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/constantNames.php');
		self::assertSame(['FOO', 'BOO'], ConstantHelper::getAllNames($phpcsFile));
	}

	public function testGetAllNamesNoNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/constantWithoutNamespace.php');
		self::assertSame(['FOO'], ConstantHelper::getAllNames($phpcsFile));
	}

}
