<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class PropertyHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	/** @var \PHP_CodeSniffer\Files\File */
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
		];
	}

	/**
	 * @dataProvider dataIsProperty
	 * @param string $variableName
	 * @param bool $isProperty
	 */
	public function testIsProperty(string $variableName, bool $isProperty)
	{
		$codeSnifferFile = $this->getTestedCodeSnifferFile();

		$variablePointer = $codeSnifferFile->findNext(T_VARIABLE, 0, null, false, $variableName);
		$this->assertSame($isProperty, PropertyHelper::isProperty($codeSnifferFile, $variablePointer));
	}

	public function testNameWithNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/propertyWithNamespace.php');
		$this->assertSame('\FooNamespace\FooClass::$fooProperty', PropertyHelper::getFullyQualifiedName($codeSnifferFile, $this->findPropertyPointerByName($codeSnifferFile, 'fooProperty')));
	}

	public function testNameWithoutsNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/propertyWithoutNamespace.php');
		$this->assertSame('\FooClass::$fooProperty', PropertyHelper::getFullyQualifiedName($codeSnifferFile, $this->findPropertyPointerByName($codeSnifferFile, 'fooProperty')));
	}

	public function testNameInAnonymousClass()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/propertyInAnonymousClass.php');
		$this->assertSame('class@anonymous::$fooProperty', PropertyHelper::getFullyQualifiedName($codeSnifferFile, $this->findPropertyPointerByName($codeSnifferFile, 'fooProperty')));
	}

	private function getTestedCodeSnifferFile(): \PHP_CodeSniffer\Files\File
	{
		if ($this->testedCodeSnifferFile === null) {
			$this->testedCodeSnifferFile = $this->getCodeSnifferFile(
				__DIR__ . '/data/propertyOrNot.php'
			);
		}
		return $this->testedCodeSnifferFile;
	}

}
