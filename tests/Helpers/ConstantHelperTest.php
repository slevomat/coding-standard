<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ConstantHelperTest extends TestCase
{

	public function testNameWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/constantWithNamespace.php');

		$constantPointer = $this->findConstantPointerByName($codeSnifferFile, 'FOO');
		self::assertSame('\FooNamespace\FOO', ConstantHelper::getFullyQualifiedName($codeSnifferFile, $constantPointer));
		self::assertSame('FOO', ConstantHelper::getName($codeSnifferFile, $constantPointer));
	}

	public function testNameWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/constantWithoutNamespace.php');

		$constantPointer = $this->findConstantPointerByName($codeSnifferFile, 'FOO');
		self::assertSame('FOO', ConstantHelper::getFullyQualifiedName($codeSnifferFile, $constantPointer));
		self::assertSame('FOO', ConstantHelper::getName($codeSnifferFile, $constantPointer));
	}

	public function testGetAllNames(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/constantNames.php');
		self::assertSame(['FOO', 'BOO'], ConstantHelper::getAllNames($codeSnifferFile));
	}

	public function testGetAllNamesNoNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/constantWithoutNamespace.php');
		self::assertSame(['FOO'], ConstantHelper::getAllNames($codeSnifferFile));
	}

}
