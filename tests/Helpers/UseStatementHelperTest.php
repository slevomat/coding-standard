<?php

namespace SlevomatCodingStandard\Helpers;

class UseStatementHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function testIsAnonymousFunctionUse()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/anonymousFunction.php'
		);
		$usePointer = $codeSnifferFile->findNext(T_USE, 0);
		$this->assertTrue(UseStatementHelper::isAnonymousFunctionUse($codeSnifferFile, $usePointer));
	}

	public function testIsNotAnonymousFunctionUse()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$usePointer = $codeSnifferFile->findNext(T_USE, 0);
		$this->assertFalse(UseStatementHelper::isAnonymousFunctionUse($codeSnifferFile, $usePointer));
	}

	public function testIsTraitUse()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/classWithTrait.php'
		);
		$usePointer = $codeSnifferFile->findNext(T_USE, 0);
		$this->assertTrue(UseStatementHelper::isTraitUse($codeSnifferFile, $usePointer));
	}

	public function testIsNotTraitUse()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$usePointer = $codeSnifferFile->findNext(T_USE, 0);
		$this->assertFalse(UseStatementHelper::isTraitUse($codeSnifferFile, $usePointer));
	}

	public function testGetNameAsReferencedInClassFromUse()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$bazUsePointer = $codeSnifferFile->findNext(T_USE, 0);
		$this->assertSame('Baz', UseStatementHelper::getNameAsReferencedInClassFromUse($codeSnifferFile, $bazUsePointer));

		$fooUsePointer = $codeSnifferFile->findNext(T_USE, $bazUsePointer + 1);
		$this->assertSame('Foo', UseStatementHelper::getNameAsReferencedInClassFromUse($codeSnifferFile, $fooUsePointer));

		$loremIpsumUsePointer = $codeSnifferFile->findNext(T_USE, $fooUsePointer + 1);
		$this->assertSame('LoremIpsum', UseStatementHelper::getNameAsReferencedInClassFromUse($codeSnifferFile, $loremIpsumUsePointer));
	}

	public function testGetFullyQualifiedTypeNameFromUse()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);
		$bazUsePointer = $codeSnifferFile->findNext(T_USE, 0);
		$this->assertSame('Bar\Baz', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $bazUsePointer));

		$fooUsePointer = $codeSnifferFile->findNext(T_USE, $bazUsePointer + 1);
		$this->assertSame('Foo', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $fooUsePointer));

		$loremIpsumUsePointer = $codeSnifferFile->findNext(T_USE, $fooUsePointer + 1);
		$this->assertSame('Lorem\Ipsum', UseStatementHelper::getFullyQualifiedTypeNameFromUse($codeSnifferFile, $loremIpsumUsePointer));
	}

	public function testGetUseStatements()
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
		$this->assertUseStatement('Rasmus\FOO_CONSTANT', 'FOO_CONSTANT', $useStatements['foo_constant'], false, true);
		$this->assertUseStatement('Lerdorf\isBar', 'isBar', $useStatements['isbar'], true, false);
	}

	/**
	 * @param string $fullyQualifiedTypeName
	 * @param string $referencedName
	 * @param \SlevomatCodingStandard\Helpers\UseStatement $useStatement
	 * @param boolean $isFunction
	 * @param boolean $isConstant
	 */
	private function assertUseStatement($fullyQualifiedTypeName, $referencedName, UseStatement $useStatement, $isFunction, $isConstant)
	{
		$this->assertSame($fullyQualifiedTypeName, $useStatement->getFullyQualifiedTypeName());
		$this->assertSame($referencedName, $useStatement->getNameAsReferencedInFile());
		$this->assertSame($isFunction, $useStatement->isFunction());
		$this->assertSame($isConstant, $useStatement->isConstant());
	}

}
