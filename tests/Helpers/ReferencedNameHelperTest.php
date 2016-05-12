<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ReferencedNameHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function dataGetAllReferencedNames(): array
	{
		return [
			[
				[
					'\ExtendedClass',
					'\ImplementedInterface',
					'\FullyQualified\SomeOtherTrait',
					'SomeTrait',
					'TypehintedName',
					'ClassInstance',
					'StaticClass',
					'\Foo\Bar\SpecificException',
					'\Foo\Bar\Baz\SomeOtherException',
				],
				false,
			],
			[
				[
					'\ExtendedClass',
					'\ImplementedInterface',
					'\FullyQualified\SomeOtherTrait',
					'SomeTrait',
					'ORM\Column',
					'Bar',
					'Lorem',
					'Ipsum',
					'Rasmus',
					'Lerdorf',
					'\Foo\BarBaz',
					'TypehintedName',
					'AnotherTypehintedName',
					'Returned_Typehinted_Underscored_Name',
					'ClassInstance',
					'StaticClass',
					'\Foo\Bar\SpecificException',
					'\Foo\Bar\Baz\SomeOtherException',
				],
				true,
			],
		];
	}

	/**
	 * @dataProvider dataGetAllReferencedNames
	 * @param string[] $foundTypes
	 * @param bool $searchAnnotations
	 */
	public function testGetAllReferencedNames(array $foundTypes, bool $searchAnnotations)
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/lotsOfReferencedNames.php'
		);

		$names = ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0, $searchAnnotations);
		$this->assertSame($foundTypes, array_values(array_unique(array_map(function (ReferencedName $name): string {
			return $name->getNameAsReferencedInFile();
		}, $names))));
	}

	public function testFindReferencedNameEndPointerOnNonReferencedName()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/fileWithoutReferencedName.php'
		);
		$stringTokenPointer = $codeSnifferFile->findNext(T_STRING, 0);
		$this->assertNull(ReferencedNameHelper::findReferencedNameEndPointer($codeSnifferFile, $stringTokenPointer));
	}

	public function testFindReferencedNameEndPointer()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/referencedName.php'
		);
		$backslashTokenPointer = $codeSnifferFile->findNext(T_NS_SEPARATOR, 0);
		$endTokenPointer = ReferencedNameHelper::findReferencedNameEndPointer($codeSnifferFile, $backslashTokenPointer);
		$this->assertTokenPointer(T_OPEN_PARENTHESIS, 3, $codeSnifferFile, $endTokenPointer);
	}

	public function testReturnTypehint()
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
