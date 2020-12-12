<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function sprintf;
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
				'$propertyPromotion',
				false,
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
			[
				'$propertyWithUnionTypeHint',
				true,
			],
			[
				'$nullableWithNullOnStart',
				true,
			],
			[
				'$nullableWithNullInTheMiddle',
				true,
			],
			[
				'$nullableWithNullAtTheEnds',
				true,
			],
			[
				'$unionWithSpaces',
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

	/**
	 * @return mixed[][]
	 */
	public function dataFindTypeHint(): array
	{
		return [
			[
				'$boolean',
				null,
				null,
			],
			[
				'$string',
				null,
				null,
			],
			[
				'$weirdDefinition',
				null,
				null,
			],
			[
				'$withTypeHint',
				'?\Whatever\Anything',
				true,
			],
			[
				'$withSimpleTypeHint',
				'int',
				false,
			],
			[
				'$typedPropertyAfterMethod',
				'string',
				false,
			],
			[
				'$propertyWithUnionTypeHint',
				'string|int',
				false,
			],
			[
				'$nullableWithNullOnStart',
				'null|int',
				true,
			],
			[
				'$nullableWithNullInTheMiddle',
				'string|null|int',
				true,
			],
			[
				'$nullableWithNullAtTheEnds',
				'string|null',
				true,
			],
			[
				'$unionWithSpaces',
				'string|int|false|null',
				true,
			],
		];
	}

	/**
	 * @dataProvider dataFindTypeHint
	 * @param string $propertyName
	 * @param string|null $typeHint
	 * @param bool|null $isNullable
	 */
	public function testFindTypeHint(string $propertyName, ?string $typeHint, ?bool $isNullable): void
	{
		$phpcsFile = $this->getTestedCodeSnifferFile();

		$propertyPointer = TokenHelper::findNextContent($phpcsFile, T_VARIABLE, $propertyName, 0);

		$propertyTypeHint = PropertyHelper::findTypeHint($phpcsFile, $propertyPointer);

		if ($typeHint === null) {
			self::assertNull($propertyTypeHint);
		} else {
			self::assertSame($typeHint, $propertyTypeHint->getTypeHint());
			self::assertSame($isNullable, $propertyTypeHint->isNullable(), sprintf('Property %s', $propertyName));
		}
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
