<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class UseStatementHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function testIsAnonymousFunctionUse(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/anonymousFunction.php'
		);
		$usePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		$this->assertTrue(UseStatementHelper::isAnonymousFunctionUse($codeSnifferFile, $usePointer));
	}

	public function testIsNotAnonymousFunctionUse(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$usePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		$this->assertFalse(UseStatementHelper::isAnonymousFunctionUse($codeSnifferFile, $usePointer));
	}

	public function testIsTraitUse(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/classWithTrait.php'
		);
		$usePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		$this->assertTrue(UseStatementHelper::isTraitUse($codeSnifferFile, $usePointer));
	}

	public function testIsTraitUseInAnonymousClass(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/anonymousClassWithTrait.php'
		);
		$usePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		$this->assertTrue(UseStatementHelper::isTraitUse($codeSnifferFile, $usePointer));
	}

	public function testIsNotTraitUse(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$usePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		$this->assertFalse(UseStatementHelper::isTraitUse($codeSnifferFile, $usePointer));
	}

	public function testGetNameAsReferencedInClassFromUse(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$bazUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		$this->assertSame('Baz', UseStatementHelper::getNameAsReferencedInClassFromUse($codeSnifferFile, $bazUsePointer));

		$fooUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $bazUsePointer + 1);
		$this->assertSame('Foo', UseStatementHelper::getNameAsReferencedInClassFromUse($codeSnifferFile, $fooUsePointer));

		$loremIpsumUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $fooUsePointer + 1);
		$this->assertSame('LoremIpsum', UseStatementHelper::getNameAsReferencedInClassFromUse($codeSnifferFile, $loremIpsumUsePointer));
	}

	public function testGetFullyQualifiedTypeNameFromUse(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$bazUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		$this->assertSame('Bar\Baz', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $bazUsePointer));

		$fooUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $bazUsePointer + 1);
		$this->assertSame('Foo', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $fooUsePointer));

		$loremIpsumUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $fooUsePointer + 1);
		$this->assertSame('Lorem\Ipsum', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $loremIpsumUsePointer));

		$rasmusFooConstantUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $loremIpsumUsePointer + 1);
		$this->assertSame('Rasmus\FOO_CONSTANT', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $rasmusFooConstantUsePointer));

		$lerdorfIsBarPointer = TokenHelper::findNext($codeSnifferFile, T_USE, $rasmusFooConstantUsePointer + 1);
		$this->assertSame('Lerdorf\isBar', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $lerdorfIsBarPointer));
	}

	public function testGetUseStatements(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$useStatements = UseStatementHelper::getUseStatements($codeSnifferFile, 0);
		$this->assertCount(6, $useStatements);
		$this->assertSame(2, $useStatements['baz']->getPointer());
		$this->assertUseStatement('Bar\Baz', 'Baz', $useStatements['baz'], false, false);
		$this->assertUseStatement('Foo', 'Foo', $useStatements['foo'], false, false);
		$this->assertUseStatement('Lorem\Ipsum', 'LoremIpsum', $useStatements['loremipsum'], false, false);
		$this->assertUseStatement('Zero', 'Zero', $useStatements['zero'], false, false);
		$this->assertUseStatement('Rasmus\FOO_CONSTANT', 'FOO_CONSTANT', $useStatements['FOO_CONSTANT'], false, true);
		$this->assertUseStatement('Lerdorf\isBar', 'isBar', $useStatements['isbar'], true, false);
	}

	private function assertUseStatement(string $fullyQualifiedTypeName, string $referencedName, UseStatement $useStatement, bool $isFunction, bool $isConstant): void
	{
		$this->assertSame($fullyQualifiedTypeName, $useStatement->getFullyQualifiedTypeName());
		$this->assertSame($referencedName, $useStatement->getNameAsReferencedInFile());
		$this->assertSame($isFunction, $useStatement->isFunction());
		$this->assertSame($isConstant, $useStatement->isConstant());
	}

}
