<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ReferencedNameHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function testGetAllReferencedNames()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/lotsOfReferencedNames.php'
		);

		$foundTypes = [
			['\ExtendedClass', false, false],
			['\ImplementedInterface', false, false],
			['\SecondImplementedInterface', false, false],
			['\ThirdImplementedInterface', false, false],
			['\FullyQualified\SomeOtherTrait', false, false],
			['SomeDifferentTrait', false, false],
			['\FullyQualified\SometTotallyDifferentTrait', false, false],
			['SomeTrait', false, false],
			['TypeHintedName', false, false],
			['ClassInstance', false, false],
			['StaticClass', false, false],
			['\Foo\Bar\SpecificException', false, false],
			['\Foo\Bar\Baz\SomeOtherException', false, false],
			['callToFunction', true, false],
			['FOO_CONSTANT', false, true],
			['BAZ_CONSTANT', false, true],
			['LoremClass', false, false],
			['IpsumClass', false, false],
			['Hoo', false, false],
			['BAR_CONSTANT', false, true],
			['Integer', false, false],
			['Boolean', false, false],
			['\ExtendedInterface', false, false],
			['\SecondExtendedInterface', false, false],
			['\ThirdExtendedInterface', false, false],
			['SomeTrait', false, false],
		];

		$names = ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0);
		$this->assertCount(count($foundTypes), $names);
		foreach ($names as $i => $referencedName) {
			$this->assertSame($foundTypes[$i][0], $referencedName->getNameAsReferencedInFile());
			$this->assertSame($foundTypes[$i][1], $referencedName->isFunction(), $foundTypes[$i][0]);
			$this->assertSame($foundTypes[$i][2], $referencedName->isConstant(), $foundTypes[$i][0]);
		}
	}

	public function testGetAllReferencedNamesWithNullableTypehints()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/referencedNamesWithNullableTypeHints.php'
		);

		$foundTypes = [
			['NullableParameterTypeHint', false, false],
			['NullableReturnTypeHint', false, false],
		];

		$names = ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0);
		$this->assertCount(count($foundTypes), $names);
		foreach ($names as $i => $referencedName) {
			$this->assertSame($foundTypes[$i][0], $referencedName->getNameAsReferencedInFile());
			$this->assertSame($foundTypes[$i][1], $referencedName->isFunction(), $foundTypes[$i][0]);
			$this->assertSame($foundTypes[$i][2], $referencedName->isConstant(), $foundTypes[$i][0]);
		}
	}

	public function testGetAllReferencedNamesOnNonReferencedName()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/fileWithoutReferencedName.php'
		);
		$this->assertCount(0, ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0));
	}

	public function testMultipleExceptionsCatch()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/multipleExceptionsCatch.php'
		);

		$foundTypes = [
			['doSomething', true, false],
			['FirstDoubleException', false, false],
			['\Foo\SecondDoubleException', false, false],
			['\Foo\Bar\FirstMultipleException', false, false],
			['SecondMultipleException', false, false],
			['ThirdMultipleException', false, false],
		];

		$names = ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0);
		$this->assertCount(count($foundTypes), $names);
		foreach ($names as $i => $referencedName) {
			$this->assertSame($foundTypes[$i][0], $referencedName->getNameAsReferencedInFile());
			$this->assertSame($foundTypes[$i][1], $referencedName->isFunction(), $foundTypes[$i][0]);
			$this->assertSame($foundTypes[$i][2], $referencedName->isConstant(), $foundTypes[$i][0]);
		}
	}

	public function testGetReferencedNameEndPointer()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/referencedName.php'
		);
		$backslashTokenPointer = $codeSnifferFile->findNext(T_NS_SEPARATOR, 0);
		$endTokenPointer = ReferencedNameHelper::getReferencedNameEndPointer($codeSnifferFile, $backslashTokenPointer);
		$this->assertTokenPointer(T_STRING, 3, $codeSnifferFile, $endTokenPointer);
	}

	public function testReturnTypeHint()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/return-typehint.php'
		);
		$names = ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0);
		$this->assertCount(2, $names);
		$this->assertSame('Bar', $names[0]->getNameAsReferencedInFile());
		$this->assertSame('\OtherNamespace\Lorem', $names[1]->getNameAsReferencedInFile());
	}

	public function testConstantIsNotReferencedName()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/class-constant.php'
		);
		$names = ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0);
		$this->assertCount(0, $names);
	}

}
