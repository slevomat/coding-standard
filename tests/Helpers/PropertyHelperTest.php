<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use const T_VARIABLE;

class PropertyHelperTest extends TestCase
{

	/** @var File */
	private $testedCodeSnifferFile;

	/**
	 * @return mixed[][]
	 */
	public function dataIsProperty(): array
	{
		return [
			[
				'$boolean',
				true,
			],
			[
				'$string',
				true,
			],
			[
				'$boo',
				false,
			],
			[
				'$hoo',
				false,
			],
			[
				'$weirdDefinition',
				true,
			],
			[
				'$withTypeHint',
				true,
			],
			[
				'$withSimpleTypeHint',
				true,
			],
			[
				'$typedPropertyAfterMethod',
				true,
			],
		];
	}

	/**
	 * @dataProvider dataIsProperty
	 * @param string $variableName
	 * @param bool $isProperty
	 */
	public function testIsProperty(string $variableName, bool $isProperty): void
	{
		$phpcsFile = $this->getTestedCodeSnifferFile();

		$variablePointer = TokenHelper::findNextContent($phpcsFile, T_VARIABLE, $variableName, 0);
		self::assertSame($isProperty, PropertyHelper::isProperty($phpcsFile, $variablePointer));
	}

	public function testNameWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/propertyWithNamespace.php');
		self::assertSame(
			'\FooNamespace\FooClass::$fooProperty',
			PropertyHelper::getFullyQualifiedName($phpcsFile, $this->findPropertyPointerByName($phpcsFile, 'fooProperty'))
		);
	}

	public function testNameWithoutsNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/propertyWithoutNamespace.php');
		self::assertSame(
			'\FooClass::$fooProperty',
			PropertyHelper::getFullyQualifiedName($phpcsFile, $this->findPropertyPointerByName($phpcsFile, 'fooProperty'))
		);
	}

	public function testNameInAnonymousClass(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/propertyInAnonymousClass.php');
		self::assertSame(
			'class@anonymous::$fooProperty',
			PropertyHelper::getFullyQualifiedName($phpcsFile, $this->findPropertyPointerByName($phpcsFile, 'fooProperty'))
		);
	}

	private function getTestedCodeSnifferFile(): File
	{
		if ($this->testedCodeSnifferFile === null) {
			$this->testedCodeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/propertyOrNot.php');
		}
		return $this->testedCodeSnifferFile;
	}

}
