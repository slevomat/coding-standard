<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use function count;
use function sprintf;
use const T_NS_SEPARATOR;
use const T_STRING;

class ReferencedNameHelperTest extends TestCase
{

	public function testGetAllReferencedNames(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/lotsOfReferencedNames.php');

		$expectedTypes = [
			['\ExtendedClass', false, false],
			['\ImplementedInterface', false, false],
			['\SecondImplementedInterface', false, false],
			['\ThirdImplementedInterface', false, false],
			['\FullyQualified\SomeOtherTrait', false, false],
			['SomeDifferentTrait', false, false],
			['\FullyQualified\SometTotallyDifferentTrait', false, false],
			['SomeTrait', false, false],
			['Enum', false, false],
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
			['string', true, false],
			['doSomething', true, false],
			['STREAM_URL_STAT_QUIET', false, true],
			['E_ALL', false, true],
			['E_NOTICE', false, true],
			['DateTimeImmutable', false, false],
			['DateTime', false, false],
			['UnionType', false, false],
			['\Union\UnionType2', false, false],
			['UnionType3', false, false],
			['\Union\UnionType4', false, false],
			['UnionType5', false, false],
			['UnionType6', false, false],
			['IntersectionType', false, false],
			['\Intersection\IntersectionType2', false, false],
			['\Intersection\IntersectionType3', false, false],
			['IntersectionType4', false, false],
			['\Intersection\IntersectionType5', false, false],
			['IntersectionType6', false, false],
			['\Intersection\IntersectionType7', false, false],
			['str_pad', true, false],
			['STR_PAD_RIGHT', false, true],
			['EnumType', false, false],
			['UrlGeneratorInterface', false, false],
			['ClassInHeredoc', false, false],
			['ClassInDoubleQuote', false, false],
			['object', true, false],
		];

		$names = ReferencedNameHelper::getAllReferencedNames($phpcsFile, 0);
		self::assertCount(count($expectedTypes), $names);
		foreach ($names as $i => $referencedName) {
			[$type, $isFunction, $isConstant] = $expectedTypes[$i];

			self::assertSame($type, $referencedName->getNameAsReferencedInFile());
			self::assertSame($isFunction, $referencedName->isFunction(), $type);
			self::assertSame($isConstant, $referencedName->isConstant(), $type);
		}
	}

	public function testGetAllReferencedNamesWithNullableTypehints(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/referencedNamesWithNullableTypeHints.php');

		$foundTypes = [
			'NullableParameterTypeHint',
			'NullableReturnTypeHint',
		];

		$names = ReferencedNameHelper::getAllReferencedNames($phpcsFile, 0);
		self::assertCount(count($foundTypes), $names);
		foreach ($names as $i => $referencedName) {
			$type = $foundTypes[$i];

			self::assertSame($type, $referencedName->getNameAsReferencedInFile());
			self::assertFalse($referencedName->isFunction(), $type);
			self::assertFalse($referencedName->isConstant(), $type);
		}
	}

	public function testGetAllReferencedNamesOnNonReferencedName(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/fileWithoutReferencedName.php');
		self::assertCount(0, ReferencedNameHelper::getAllReferencedNames($phpcsFile, 0));
	}

	public function testMultipleExceptionsCatch(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/multipleExceptionsCatch.php');

		$foundTypes = [
			['doSomething', true],
			['FirstDoubleException', false],
			['\Foo\SecondDoubleException', false],
			['\Foo\Bar\FirstMultipleException', false],
			['SecondMultipleException', false],
			['ThirdMultipleException', false],
		];

		$names = ReferencedNameHelper::getAllReferencedNames($phpcsFile, 0);
		self::assertCount(count($foundTypes), $names);
		foreach ($names as $i => $referencedName) {
			[$type, $isFunction] = $foundTypes[$i];

			self::assertSame($type, $referencedName->getNameAsReferencedInFile());
			self::assertSame($isFunction, $referencedName->isFunction(), $type);
			self::assertFalse($referencedName->isConstant(), $type);
		}
	}

	public function testGetReferencedNameEndPointer(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/referencedName.php');

		$backslashTokenPointer = TokenHelper::findNext($phpcsFile, T_NS_SEPARATOR, 0);
		$endTokenPointer = ReferencedNameHelper::getReferencedNameEndPointer($phpcsFile, $backslashTokenPointer);
		self::assertTokenPointer(T_STRING, 3, $phpcsFile, $endTokenPointer);
	}

	public function testReturnTypeHint(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/return-typehint.php');
		$names = ReferencedNameHelper::getAllReferencedNames($phpcsFile, 0);
		self::assertCount(2, $names);
		self::assertSame('Bar', $names[0]->getNameAsReferencedInFile());
		self::assertSame('\OtherNamespace\Lorem', $names[1]->getNameAsReferencedInFile());
	}

	public function testConstantIsNotReferencedName(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/classConstant.php');
		$names = ReferencedNameHelper::getAllReferencedNames($phpcsFile, 0);
		self::assertCount(0, $names);
	}

	public function testEnumCaseIsNotReferencedName(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/enumCase.php');
		$names = ReferencedNameHelper::getAllReferencedNames($phpcsFile, 0);
		self::assertCount(0, $names);
	}

	public function testMethodReturningReferenceIsNotReferencedName(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/methodReturnsReference.php');
		$names = ReferencedNameHelper::getAllReferencedNames($phpcsFile, 0);
		self::assertCount(0, $names);
	}

	public function testGotoLabelIsNotReferencedName(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/goto.php');
		$names = ReferencedNameHelper::getAllReferencedNames($phpcsFile, 0);
		self::assertCount(0, $names);
	}

	public function testUnionTypeHints(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/unionTypeHints.php');

		$expectedNames = [
			'IntClass',
			'IntInterface',
			'StringClass',
			'StringInterface',
			'Anything',
			'Nothing',
			'DateTimeImmutable',
			'Something',
			'DateTime',
		];

		$names = ReferencedNameHelper::getAllReferencedNames($phpcsFile, 0);

		self::assertCount(count($expectedNames), $names);

		foreach ($expectedNames as $no => $expectedName) {
			self::assertSame($expectedName, $names[$no]->getNameAsReferencedInFile());
			self::assertTrue(
				$names[$no]->isClass(),
				sprintf('%s should be class, but %s found', $names[$no]->getNameAsReferencedInFile(), $names[$no]->getType())
			);
		}
	}

	public function testGetAllReferencedNamesInAttributes(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/referencedNamesFromAttributes.php');

		$expectedTypes = [
			['Something', false],
			['Anything', false],
			['\Whatever\Anything', false],
			['PHP_VERSION', true],
			['Nothing', false],
			['\Doctrine\Column', false],
			['\Doctrine\Column\Properties', false],
			['Attribute1', false],
			['Attribute2', false],
			['\Nette\DI\Attributes\Inject', false],
		];

		$names = ReferencedNameHelper::getAllReferencedNamesInAttributes($phpcsFile, 0);

		self::assertCount(count($expectedTypes), $names);

		foreach ($names as $i => $referencedName) {
			[$type, $isConstant] = $expectedTypes[$i];

			self::assertSame($type, $referencedName->getNameAsReferencedInFile());
			self::assertSame($isConstant, $referencedName->isConstant(), $type);
		}
	}

}
