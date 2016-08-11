<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class FunctionHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function testNameWithNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionWithNamespace.php');
		$this->assertSame('\FooNamespace\fooFunction', FunctionHelper::getFullyQualifiedName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooFunction')));
		$this->assertSame('fooFunction', FunctionHelper::getName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooFunction')));
		$this->assertSame('\FooNamespace\FooClass::fooMethod', FunctionHelper::getFullyQualifiedName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
		$this->assertSame('fooMethod', FunctionHelper::getName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
	}

	public function testNameWithoutNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionWithoutNamespace.php');
		$this->assertSame('fooFunction', FunctionHelper::getFullyQualifiedName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooFunction')));
		$this->assertSame('fooFunction', FunctionHelper::getName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooFunction')));
		$this->assertSame('\FooClass::fooMethod', FunctionHelper::getFullyQualifiedName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
		$this->assertSame('fooMethod', FunctionHelper::getName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
	}

	public function testNameInAnonymousClass()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionInAnonymousClass.php');
		$this->assertSame('class@anonymous::fooMethod', FunctionHelper::getFullyQualifiedName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
		$this->assertSame('fooMethod', FunctionHelper::getName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
	}

	public function testAbstractOrNot()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionAbstractOrNot.php');
		$this->assertFalse(FunctionHelper::isAbstract($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
		$this->assertTrue(FunctionHelper::isAbstract($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooAbstractMethod')));
		$this->assertTrue(FunctionHelper::isAbstract($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooInterfaceMethod')));
	}

	public function testFunctionOrMethod()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionOrMethod.php');
		$this->assertTrue(FunctionHelper::isMethod($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
		$this->assertFalse(FunctionHelper::isMethod($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooFunction')));
	}

	public function dataParametersTypeHints(): array
	{
		return [
			[
				'allParametersWithTypeHints',
				[
					'$string' => 'string',
					'$int' => 'int',
					'$bool' => 'bool',
					'$float' => 'float',
					'$callable' => 'callable',
					'$array' => 'array',
					'$object' => '\FooNamespace\FooClass',
				],
				[],
			],
			[
				'allParametersWithoutTypeHints',
				[
					'$string' => null,
					'$int' => null,
					'$bool' => null,
					'$float' => null,
					'$callable' => null,
					'$array' => null,
					'$object' => null,
				],
				[
					'$string',
					'$int',
					'$bool',
					'$float',
					'$callable',
					'$array',
					'$object',
				],
			],
			[
				'someParametersWithoutTypeHints',
				[
					'$string' => 'string',
					'$int' => null,
					'$bool' => 'bool',
					'$float' => null,
					'$callable' => 'callable',
					'$array' => null,
					'$object' => '\FooNamespace\FooClass',
				],
				[
					'$int',
					'$float',
					'$array',
				],
			],
		];
	}

	/**
	 * @dataProvider dataParametersTypeHints
	 * @param string $functionName
	 * @param string[] $expectedParametersTypeHints
	 * @param string[] $expectedParametersWithoutTypeHints
	 */
	public function testParametersTypeHints(
		string $functionName,
		array $expectedParametersTypeHints,
		array $expectedParametersWithoutTypeHints
	)
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionParametersTypeHints.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, $functionName);
		$this->assertSame($expectedParametersTypeHints, FunctionHelper::getParametersTypeHints($codeSnifferFile, $functionPointer));
		$this->assertSame($expectedParametersWithoutTypeHints, FunctionHelper::getParametersWithoutTypeHint($codeSnifferFile, $functionPointer));
	}

	public function dataReturnsValueOrNot(): array
	{
		return [
			['returnsValue', true],
			['returnsVariable', true],
			['returnsValueInCondition', true],
			['noReturn', false],
			['returnsVoid', false],
			['containsClosure', false],
			['containsAnonymousClass', false],
		];
	}

	/**
	 * @dataProvider dataReturnsValueOrNot
	 * @param string $functionName
	 * @param bool $returnsValue
	 */
	public function testReturnsValueOrNot(
		string $functionName,
		bool $returnsValue
	)
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionReturnsValueOrNot.php');
		$this->assertSame($returnsValue, FunctionHelper::returnsValue($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, $functionName)));
	}

	public function testReturnTypeHint()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionReturnTypeHint.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'withReturnTypeHint');
		$this->assertTrue(FunctionHelper::hasReturnTypeHint($codeSnifferFile, $functionPointer));
		$this->assertSame('\FooNamespace\FooInterface', FunctionHelper::findReturnTypeHint($codeSnifferFile, $functionPointer));

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'withoutReturnTypeHint');
		$this->assertFalse(FunctionHelper::hasReturnTypeHint($codeSnifferFile, $functionPointer));
		$this->assertNull(FunctionHelper::findReturnTypeHint($codeSnifferFile, $functionPointer));

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'abstractWithReturnTypeHint');
		$this->assertTrue(FunctionHelper::hasReturnTypeHint($codeSnifferFile, $functionPointer));
		$this->assertSame('bool', FunctionHelper::findReturnTypeHint($codeSnifferFile, $functionPointer));

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'abstractWithoutReturnTypeHint');
		$this->assertFalse(FunctionHelper::hasReturnTypeHint($codeSnifferFile, $functionPointer));
		$this->assertNull(FunctionHelper::findReturnTypeHint($codeSnifferFile, $functionPointer));
	}

	public function testAnnotations()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionAnnotations.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'withAnnotations');
		$this->assertSame([
			'string $a',
			'int $b',
		], FunctionHelper::getParametersAnnotations($codeSnifferFile, $functionPointer));
		$this->assertSame('bool', FunctionHelper::findReturnAnnotation($codeSnifferFile, $functionPointer));

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'withoutAnnotations');
		$this->assertCount(0, FunctionHelper::getParametersAnnotations($codeSnifferFile, $functionPointer));
		$this->assertNull(FunctionHelper::findReturnAnnotation($codeSnifferFile, $functionPointer));
	}

	public function testFindCurrentParentClassName()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/classWithExtends.php'
		);
		$parentClassName = FunctionHelper::findCurrentParentClassName(
			$codeSnifferFile,
			TokenHelper::getLastTokenPointer($codeSnifferFile)
		);
		$this->assertSame('\Exception', $parentClassName);
	}

	public function testFindCurrentParentInterfaceNames()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/classWithInterfaces.php'
		);
		$parentInterfaceNames = FunctionHelper::findCurrentParentInterfaceNames(
			$codeSnifferFile,
			TokenHelper::getLastTokenPointer($codeSnifferFile)
		);
		$this->assertSame(['Foo', 'Bar'], $parentInterfaceNames);
	}

}
