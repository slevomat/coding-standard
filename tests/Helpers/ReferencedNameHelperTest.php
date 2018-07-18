<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use const T_NS_SEPARATOR;
use const T_STRING;
use function count;

class ReferencedNameHelperTest extends TestCase
{

	public function testGetAllReferencedNames(): void
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
			['SomeClass', false, false],
			['TYPE_ONE', false, true],
			['ArrayKey1', false, false],
			['ArrayKey2', false, false],
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
			['Bobobo', false, false],
			['Dododo', false, false],
			['\ExtendedInterface', false, false],
			['\SecondExtendedInterface', false, false],
			['\ThirdExtendedInterface', false, false],
			['SomeTrait', false, false],
			['OPENSSL_ALGO_SHA256', false, true],
			['OPENSSL_ALGO_SHA512', false, true],
			['SomeTrait', false, false],
		];

		$names = ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0);
		self::assertCount(count($foundTypes), $names);
		foreach ($names as $i => $referencedName) {
			[$type, $isFunction, $isConstant] = $foundTypes[$i];

			self::assertSame($type, $referencedName->getNameAsReferencedInFile());
			self::assertSame($isFunction, $referencedName->isFunction(), $type);
			self::assertSame($isConstant, $referencedName->isConstant(), $type);
		}
	}

	public function testGetAllReferencedNamesWithNullableTypehints(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/referencedNamesWithNullableTypeHints.php'
		);

		$foundTypes = [
			['NullableParameterTypeHint', false, false],
			['NullableReturnTypeHint', false, false],
		];

		$names = ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0);
		self::assertCount(count($foundTypes), $names);
		foreach ($names as $i => $referencedName) {
			[$type, $isFunction, $isConstant] = $foundTypes[$i];

			self::assertSame($type, $referencedName->getNameAsReferencedInFile());
			self::assertSame($isFunction, $referencedName->isFunction(), $type);
			self::assertSame($isConstant, $referencedName->isConstant(), $type);
		}
	}

	public function testGetAllReferencedNamesOnNonReferencedName(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/fileWithoutReferencedName.php'
		);
		self::assertCount(0, ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0));
	}

	public function testMultipleExceptionsCatch(): void
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
		self::assertCount(count($foundTypes), $names);
		foreach ($names as $i => $referencedName) {
			[$type, $isFunction, $isConstant] = $foundTypes[$i];

			self::assertSame($type, $referencedName->getNameAsReferencedInFile());
			self::assertSame($isFunction, $referencedName->isFunction(), $type);
			self::assertSame($isConstant, $referencedName->isConstant(), $type);
		}
	}

	public function testGetReferencedNameEndPointer(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/referencedName.php'
		);
		$backslashTokenPointer = TokenHelper::findNext($codeSnifferFile, T_NS_SEPARATOR, 0);
		$endTokenPointer = ReferencedNameHelper::getReferencedNameEndPointer($codeSnifferFile, $backslashTokenPointer);
		self::assertTokenPointer(T_STRING, 3, $codeSnifferFile, $endTokenPointer);
	}

	public function testReturnTypeHint(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/return-typehint.php'
		);
		$names = ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0);
		self::assertCount(2, $names);
		self::assertSame('Bar', $names[0]->getNameAsReferencedInFile());
		self::assertSame('\OtherNamespace\Lorem', $names[1]->getNameAsReferencedInFile());
	}

	public function testConstantIsNotReferencedName(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/classConstant.php'
		);
		$names = ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0);
		self::assertCount(0, $names);
	}

	public function testMethodReturningReferenceIsNotReferencedName(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/methodReturnsReference.php'
		);
		$names = ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0);
		self::assertCount(0, $names);
	}

	public function testGotoLabelIsNotReferencedName(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/goto.php'
		);
		$names = ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0);
		self::assertCount(0, $names);
	}

}
