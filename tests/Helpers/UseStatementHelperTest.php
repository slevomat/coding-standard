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

		$lerdorfIsBarConstantUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $loremIpsumUsePointer + 1);
		$this->assertSame('Lerdorf\IS_BAR', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $lerdorfIsBarConstantUsePointer));

		$rasmusFooConstantUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $lerdorfIsBarConstantUsePointer + 1);
		$this->assertSame('Rasmus\FOO', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $rasmusFooConstantUsePointer));

		$lerdorfIsBarFunctionUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $rasmusFooConstantUsePointer + 1);
		$this->assertSame('Lerdorf\isBar', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $lerdorfIsBarFunctionUsePointer));

		$rasmusFooFunctionUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $lerdorfIsBarFunctionUsePointer + 1);
		$this->assertSame('Rasmus\foo', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $rasmusFooFunctionUsePointer));
	}

	public function testGetUseStatements(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$useStatements = UseStatementHelper::getUseStatements($codeSnifferFile, 0);
		$this->assertCount(8, $useStatements);
		$this->assertSame(2, $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_DEFAULT, 'Baz')]->getPointer());
		$this->assertUseStatement('Bar\Baz', 'Baz', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_DEFAULT, 'Baz')], false, false);
		$this->assertUseStatement('Foo', 'Foo', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_DEFAULT, 'Foo')], false, false);
		$this->assertUseStatement('Lorem\Ipsum', 'LoremIpsum', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_DEFAULT, 'LoremIpsum')], false, false);
		$this->assertUseStatement('Zero', 'Zero', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_DEFAULT, 'Zero')], false, false);
		$this->assertUseStatement('Rasmus\FOO', 'FOO', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_CONSTANT, 'FOO')], false, true);
		$this->assertUseStatement('Rasmus\foo', 'foo', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_FUNCTION, 'foo')], true, false);
		$this->assertUseStatement('Lerdorf\IS_BAR', 'IS_BAR', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_CONSTANT, 'IS_BAR')], false, true);
		$this->assertUseStatement('Lerdorf\isBar', 'isBar', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_FUNCTION, 'isBar')], true, false);
	}

	private function assertUseStatement(string $fullyQualifiedTypeName, string $referencedName, UseStatement $useStatement, bool $isFunction, bool $isConstant): void
	{
		$this->assertSame($fullyQualifiedTypeName, $useStatement->getFullyQualifiedTypeName());
		$this->assertSame($referencedName, $useStatement->getNameAsReferencedInFile());
		$this->assertSame($isFunction, $useStatement->isFunction());
		$this->assertSame($isConstant, $useStatement->isConstant());
	}

}
