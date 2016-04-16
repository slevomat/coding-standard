<?php

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
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/php7/functionInAnonymousClass.php');
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

	public function testParametersTypeHints()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/php7/functionParametersTypeHints.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'allParametersWithTypeHints');
		$parametersTypeHints = FunctionHelper::getParametersTypeHints($codeSnifferFile, $functionPointer);
		$parametersWithoutTypeHints = FunctionHelper::getParametersWithoutTypeHint($codeSnifferFile, $functionPointer);
		$this->assertCount(7, $parametersTypeHints);
		$this->assertSame([
			'$string' => 'string',
			'$int' => 'int',
			'$bool' => 'bool',
			'$float' => 'float',
			'$callable' => 'callable',
			'$array' => 'array',
			'$object' => '\FooNamespace\FooClass',
		], $parametersTypeHints);
		$this->assertCount(0, $parametersWithoutTypeHints);

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'allParametersWithoutTypeHints');
		$parametersTypeHints = FunctionHelper::getParametersTypeHints($codeSnifferFile, $functionPointer);
		$parametersWithoutTypeHints = FunctionHelper::getParametersWithoutTypeHint($codeSnifferFile, $functionPointer);
		$this->assertCount(7, $parametersTypeHints);
		$this->assertSame([
			'$string' => null,
			'$int' => null,
			'$bool' => null,
			'$float' => null,
			'$callable' => null,
			'$array' => null,
			'$object' => null,
		], $parametersTypeHints);
		$this->assertCount(7, $parametersWithoutTypeHints);
		$this->assertSame([
			'$string',
			'$int',
			'$bool',
			'$float',
			'$callable',
			'$array',
			'$object',
		], $parametersWithoutTypeHints);

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'someParametersWithoutTypeHints');
		$parametersTypeHints = FunctionHelper::getParametersTypeHints($codeSnifferFile, $functionPointer);
		$parametersWithoutTypeHints = FunctionHelper::getParametersWithoutTypeHint($codeSnifferFile, $functionPointer);
		$this->assertCount(7, $parametersTypeHints);
		$this->assertSame([
			'$string' => 'string',
			'$int' => null,
			'$bool' => 'bool',
			'$float' => null,
			'$callable' => 'callable',
			'$array' => null,
			'$object' => '\FooNamespace\FooClass',
		], $parametersTypeHints);
		$this->assertCount(3, $parametersWithoutTypeHints);
		$this->assertSame([
			'$int',
			'$float',
			'$array',
		], $parametersWithoutTypeHints);
	}

	public function testReturnsValueOrNot()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionReturnsValueOrNot.php');
		$this->assertTrue(FunctionHelper::returnsValue($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'returnsValue')));
		$this->assertTrue(FunctionHelper::returnsValue($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'returnsVariable')));
		$this->assertTrue(FunctionHelper::returnsValue($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'returnsValueInCondition')));
		$this->assertFalse(FunctionHelper::returnsValue($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'noReturn')));
		$this->assertFalse(FunctionHelper::returnsValue($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'returnsVoid')));
		$this->assertFalse(FunctionHelper::returnsValue($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'containsClosure')));
		$this->assertFalse(FunctionHelper::returnsValue($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'containsAnonymousClass')));
	}

	public function testReturnTypeHint()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/php7/functionReturnTypeHint.php');

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
			'integer $b',
		], FunctionHelper::getParametersAnnotations($codeSnifferFile, $functionPointer));
		$this->assertSame('boolean', FunctionHelper::findReturnAnnotation($codeSnifferFile, $functionPointer));

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'withoutAnnotations');
		$this->assertCount(0, FunctionHelper::getParametersAnnotations($codeSnifferFile, $functionPointer));
		$this->assertNull(FunctionHelper::findReturnAnnotation($codeSnifferFile, $functionPointer));
	}

}
