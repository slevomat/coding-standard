<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use const T_CLASS;
use const T_FUNCTION;
use const T_USE;

class UseStatementHelperTest extends TestCase
{

	public function testIsAnonymousFunctionUse(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/anonymousFunction.php'
		);
		$usePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		self::assertTrue(UseStatementHelper::isAnonymousFunctionUse($codeSnifferFile, $usePointer));
	}

	public function testIsNotAnonymousFunctionUse(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$usePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		self::assertFalse(UseStatementHelper::isAnonymousFunctionUse($codeSnifferFile, $usePointer));
	}

	public function testIsTraitUse(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/classWithTrait.php'
		);
		$usePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		self::assertTrue(UseStatementHelper::isTraitUse($codeSnifferFile, $usePointer));
	}

	public function testIsTraitUseInAnonymousClass(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/anonymousClassWithTrait.php'
		);
		$usePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		self::assertTrue(UseStatementHelper::isTraitUse($codeSnifferFile, $usePointer));
	}

	public function testIsNotTraitUse(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$usePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		self::assertFalse(UseStatementHelper::isTraitUse($codeSnifferFile, $usePointer));

		$classPointer = TokenHelper::findNext($codeSnifferFile, T_CLASS, 0);
		$methodPointer = TokenHelper::findNext($codeSnifferFile, T_FUNCTION, $classPointer);
		$usePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $methodPointer);
		$this->assertFalse(UseStatementHelper::isTraitUse($codeSnifferFile, $usePointer));
	}

	public function testGetAlias(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$bazUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		self::assertNull(UseStatementHelper::getAlias($codeSnifferFile, $bazUsePointer));

		$fooUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $bazUsePointer + 1);
		self::assertNull(UseStatementHelper::getAlias($codeSnifferFile, $fooUsePointer));

		$loremIpsumUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $fooUsePointer + 1);
		self::assertSame('LoremIpsum', UseStatementHelper::getAlias($codeSnifferFile, $loremIpsumUsePointer));
	}

	public function testGetNameAsReferencedInClassFromUse(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$bazUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		self::assertSame('Baz', UseStatementHelper::getNameAsReferencedInClassFromUse($codeSnifferFile, $bazUsePointer));

		$fooUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $bazUsePointer + 1);
		self::assertSame('Foo', UseStatementHelper::getNameAsReferencedInClassFromUse($codeSnifferFile, $fooUsePointer));

		$loremIpsumUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $fooUsePointer + 1);
		self::assertSame('LoremIpsum', UseStatementHelper::getNameAsReferencedInClassFromUse($codeSnifferFile, $loremIpsumUsePointer));
	}

	public function testGetFullyQualifiedTypeNameFromUse(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$bazUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, 0);
		self::assertSame('Bar\Baz', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $bazUsePointer));

		$fooUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $bazUsePointer + 1);
		self::assertSame('Foo', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $fooUsePointer));

		$loremIpsumUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $fooUsePointer + 1);
		self::assertSame('Lorem\Ipsum', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $loremIpsumUsePointer));

		$lerdorfIsBarConstantUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $loremIpsumUsePointer + 1);
		self::assertSame('Lerdorf\IS_BAR', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $lerdorfIsBarConstantUsePointer));

		$rasmusFooConstantUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $lerdorfIsBarConstantUsePointer + 1);
		self::assertSame('Rasmus\FOO', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $rasmusFooConstantUsePointer));

		$lerdorfIsBarFunctionUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $rasmusFooConstantUsePointer + 1);
		self::assertSame('Lerdorf\isBar', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $lerdorfIsBarFunctionUsePointer));

		$rasmusFooFunctionUsePointer = TokenHelper::findNext($codeSnifferFile, T_USE, $lerdorfIsBarFunctionUsePointer + 1);
		self::assertSame('Rasmus\foo', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $rasmusFooFunctionUsePointer));
	}

	public function testGetUseStatements(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$useStatements = UseStatementHelper::getUseStatements($codeSnifferFile, 0);
		self::assertCount(8, $useStatements);
		self::assertSame(2, $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_DEFAULT, 'Baz')]->getPointer());
		self::assertUseStatement('Bar\Baz', 'Baz', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_DEFAULT, 'Baz')], false, false);
		self::assertUseStatement('Foo', 'Foo', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_DEFAULT, 'Foo')], false, false);
		self::assertUseStatement('Lorem\Ipsum', 'LoremIpsum', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_DEFAULT, 'LoremIpsum')], false, false);
		self::assertUseStatement('Zero', 'Zero', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_DEFAULT, 'Zero')], false, false);
		self::assertUseStatement('Rasmus\FOO', 'FOO', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_CONSTANT, 'FOO')], false, true);
		self::assertUseStatement('Rasmus\foo', 'foo', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_FUNCTION, 'foo')], true, false);
		self::assertUseStatement('Lerdorf\IS_BAR', 'IS_BAR', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_CONSTANT, 'IS_BAR')], false, true);
		self::assertUseStatement('Lerdorf\isBar', 'isBar', $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_FUNCTION, 'isBar')], true, false);
	}

	private function assertUseStatement(string $fullyQualifiedTypeName, string $referencedName, UseStatement $useStatement, bool $isFunction, bool $isConstant): void
	{
		self::assertSame($fullyQualifiedTypeName, $useStatement->getFullyQualifiedTypeName());
		self::assertSame($referencedName, $useStatement->getNameAsReferencedInFile());
		self::assertSame($isFunction, $useStatement->isFunction());
		self::assertSame($isConstant, $useStatement->isConstant());
	}

}
