<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ConstantHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function testNameWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/constantWithNamespace.php');

		$constantPointer = $this->findConstantPointerByName($codeSnifferFile, 'FOO');
		$this->assertSame('\FooNamespace\FOO', ConstantHelper::getFullyQualifiedName($codeSnifferFile, $constantPointer));
		$this->assertSame('FOO', ConstantHelper::getName($codeSnifferFile, $constantPointer));
	}

	public function testNameWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/constantWithoutNamespace.php');

		$constantPointer = $this->findConstantPointerByName($codeSnifferFile, 'FOO');
		$this->assertSame('FOO', ConstantHelper::getFullyQualifiedName($codeSnifferFile, $constantPointer));
		$this->assertSame('FOO', ConstantHelper::getName($codeSnifferFile, $constantPointer));
	}

	public function testGetAllNames(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/constantNames.php');
		$this->assertSame(['FOO', 'BOO'], ConstantHelper::getAllNames($codeSnifferFile));
	}

}
