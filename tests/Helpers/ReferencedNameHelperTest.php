<?php

namespace SlevomatCodingStandard\Helpers;

class ReferencedNameHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function dataGetAllReferencedNames()
	{
		return [
			[
				[
					['\ExtendedClass', false, false],
					['\ImplementedInterface', false, false],
					['\FullyQualified\SomeOtherTrait', false, false],
					['SomeTrait', false, false],
					['TypehintedName', false, false],
					['ClassInstance', false, false],
					['StaticClass', false, false],
					['\Foo\Bar\SpecificException', false, false],
					['\Foo\Bar\Baz\SomeOtherException', false, false],
					['callToFunction', true, false],
					['FOO_CONSTANT', false, true],
					['BAZ_CONSTANT', false, true],
					['LoremClass', false, false],
					['IpsumClass', false, false],
				],
				false,
			],
			[
				[
					['\ExtendedClass', false, false],
					['\ImplementedInterface', false, false],
					['\FullyQualified\SomeOtherTrait', false, false],
					['SomeTrait', false, false],
					['ORM\Column', false, false],
					['Bar', false, false],
					['Lorem', false, false],
					['Ipsum', false, false],
					['Rasmus', false, false],
					['Lerdorf', false, false],
					['\Foo\BarBaz', false, false],
					['TypehintedName', false, false],
					['AnotherTypehintedName', false, false],
					['Returned_Typehinted_Underscored_Name', false, false],
					['TypehintedName', false, false],
					['ClassInstance', false, false],
					['StaticClass', false, false],
					['\Foo\Bar\SpecificException', false, false],
					['\Foo\Bar\Baz\SomeOtherException', false, false],
					['callToFunction', true, false],
					['FOO_CONSTANT', false, true],
					['BAZ_CONSTANT', false, true],
					['LoremClass', false, false],
					['IpsumClass', false, false],
				],
				true,
			],
		];
	}

	/**
	 * @dataProvider dataGetAllReferencedNames
	 * @param string[] $foundTypes
	 * @param boolean $searchAnnotations
	 */
	public function testGetAllReferencedNames(array $foundTypes, $searchAnnotations)
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/lotsOfReferencedNames.php'
		);

		$names = ReferencedNameHelper::getAllReferencedNames($codeSnifferFile, 0, $searchAnnotations);
		$this->assertCount(count($foundTypes), $names);
		foreach ($names as $i => $referencedName) {
			$this->assertSame($foundTypes[$i][0], $referencedName->getNameAsReferencedInFile());
			$this->assertSame($foundTypes[$i][1], $referencedName->isFunction(), $foundTypes[$i][0]);
			$this->assertSame($foundTypes[$i][2], $referencedName->isConstant(), $foundTypes[$i][0]);
		}
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
		if (PHP_VERSION_ID < 70000) {
			$this->markTestSkipped('Available on PHP7 only');
		}

		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/php7/return-typehint.php'
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
