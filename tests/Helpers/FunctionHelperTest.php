<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use const T_CLOSURE;
use function array_map;

class FunctionHelperTest extends TestCase
{

	public function testNameWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionWithNamespace.php');
		self::assertSame('\FooNamespace\fooFunction', FunctionHelper::getFullyQualifiedName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooFunction')));
		self::assertSame('fooFunction', FunctionHelper::getName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooFunction')));
		self::assertSame('\FooNamespace\FooClass::fooMethod', FunctionHelper::getFullyQualifiedName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
		self::assertSame('fooMethod', FunctionHelper::getName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
	}

	public function testNameWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionWithoutNamespace.php');
		self::assertSame('fooFunction', FunctionHelper::getFullyQualifiedName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooFunction')));
		self::assertSame('fooFunction', FunctionHelper::getName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooFunction')));
		self::assertSame('\FooClass::fooMethod', FunctionHelper::getFullyQualifiedName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
		self::assertSame('fooMethod', FunctionHelper::getName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
	}

	public function testNameInAnonymousClass(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionInAnonymousClass.php');
		self::assertSame('class@anonymous::fooMethod', FunctionHelper::getFullyQualifiedName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
		self::assertSame('fooMethod', FunctionHelper::getName($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
	}

	public function testAbstractOrNot(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionAbstractOrNot.php');
		self::assertFalse(FunctionHelper::isAbstract($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
		self::assertTrue(FunctionHelper::isAbstract($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooAbstractMethod')));
		self::assertTrue(FunctionHelper::isAbstract($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooInterfaceMethod')));
	}

	public function testFunctionOrMethod(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionOrMethod.php');
		self::assertTrue(FunctionHelper::isMethod($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooMethod')));
		self::assertFalse(FunctionHelper::isMethod($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooFunction')));
		self::assertFalse(FunctionHelper::isMethod($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionInCondition')));
	}

	/**
	 * @return mixed[][]
	 */
	public function dataParametersNames(): array
	{
		return [
			[
				'allParametersWithTypeHints',
				['$string', '$int', '$bool', '$float', '$callable', '$array', '$object'],
			],
			[
				'allParametersWithoutTypeHints',
				['$string', '$int', '$bool', '$float', '$callable', '$array', '$object'],
			],
			[
				'someParametersWithoutTypeHints',
				['$string', '$int', '$bool', '$float', '$callable', '$array', '$object'],
			],
			[
				'allParametersWithNullableTypeHints',
				['$string', '$int', '$bool', '$float', '$callable', '$array', '$object'],
			],
			[
				'someParametersWithNullableTypeHints',
				['$string', '$int', '$bool', '$float', '$callable', '$array', '$object'],
			],
			[
				'parametersWithWeirdDefinition',
				['$string', '$int', '$bool', '$float', '$callable', '$array', '$object'],
			],
		];
	}

	/**
	 * @dataProvider dataParametersNames
	 * @param string $functionName
	 * @param string[] $expectedParametersNames
	 */
	public function testParametersNames(
		string $functionName,
		array $expectedParametersNames
	): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionParametersNames.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, $functionName);
		self::assertSame($expectedParametersNames, FunctionHelper::getParametersNames($codeSnifferFile, $functionPointer));
	}

	/**
	 * @return mixed[][]
	 */
	public function dataParametersTypeHints(): array
	{
		return [
			[
				'allParametersWithTypeHints',
				[
					'$string' => new ParameterTypeHint('string', false, false),
					'$int' => new ParameterTypeHint('int', false, true),
					'$bool' => new ParameterTypeHint('bool', false, false),
					'$float' => new ParameterTypeHint('float', false, true),
					'$callable' => new ParameterTypeHint('callable', false, false),
					'$array' => new ParameterTypeHint('array', false, false),
					'$object' => new ParameterTypeHint('\FooNamespace\FooClass', false, true),
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
					'$string' => new ParameterTypeHint('string', false, false),
					'$int' => null,
					'$bool' => new ParameterTypeHint('bool', false, true),
					'$float' => null,
					'$callable' => new ParameterTypeHint('callable', false, false),
					'$array' => null,
					'$object' => new ParameterTypeHint('\FooNamespace\FooClass', false, false),
				],
				[
					'$int',
					'$float',
					'$array',
				],
			],
			[
				'parametersWithWeirdDefinition',
				[
					'$string' => new ParameterTypeHint('string', false, false),
					'$int' => null,
					'$bool' => new ParameterTypeHint('bool', false, true),
					'$float' => null,
					'$callable' => new ParameterTypeHint('callable', false, false),
					'$array' => null,
					'$object' => new ParameterTypeHint('\FooNamespace\FooClass', false, false),
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
	 * @return mixed[][]
	 */
	public function dataParametersNullableTypeHints(): array
	{
		return [
			[
				'allParametersWithNullableTypeHints',
				[
					'$string' => new ParameterTypeHint('string', true, false),
					'$int' => new ParameterTypeHint('int', true, true),
					'$bool' => new ParameterTypeHint('bool', true, false),
					'$float' => new ParameterTypeHint('float', true, true),
					'$callable' => new ParameterTypeHint('callable', true, false),
					'$array' => new ParameterTypeHint('array', true, false),
					'$object' => new ParameterTypeHint('\FooNamespace\FooClass', true, true),
				],
			],
			[
				'someParametersWithNullableTypeHints',
				[
					'$string' => new ParameterTypeHint('string', true, false),
					'$int' => new ParameterTypeHint('int', false, false),
					'$bool' => new ParameterTypeHint('bool', true, true),
					'$float' => new ParameterTypeHint('float', false, false),
					'$callable' => new ParameterTypeHint('callable', true, false),
					'$array' => new ParameterTypeHint('array', false, true),
					'$object' => new ParameterTypeHint('\FooNamespace\FooClass', true, false),
				],
			],
			[
				'parametersWithWeirdDefinition',
				[
					'$string' => new ParameterTypeHint('string', true, false),
					'$int' => new ParameterTypeHint('int', false, false),
					'$bool' => new ParameterTypeHint('bool', true, true),
					'$float' => new ParameterTypeHint('float', false, false),
					'$callable' => new ParameterTypeHint('callable', true, false),
					'$array' => new ParameterTypeHint('array', false, true),
					'$object' => new ParameterTypeHint('\FooNamespace\FooClass', true, false),
				],
			],
		];
	}

	/**
	 * @dataProvider dataParametersTypeHints
	 * @param string $functionName
	 * @param \SlevomatCodingStandard\Helpers\ParameterTypeHint[]|null[] $expectedParametersTypeHints
	 * @param string[] $expectedParametersWithoutTypeHints
	 */
	public function testParametersTypeHints(
		string $functionName,
		array $expectedParametersTypeHints,
		array $expectedParametersWithoutTypeHints
	): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionParametersTypeHints.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, $functionName);
		$parametersTypeHints = FunctionHelper::getParametersTypeHints($codeSnifferFile, $functionPointer);
		foreach ($expectedParametersTypeHints as $expectedParameterName => $expectedParameterTypeHint) {
			self::assertArrayHasKey($expectedParameterName, $parametersTypeHints);
			$parameterTypeHint = $parametersTypeHints[$expectedParameterName];
			if ($expectedParameterTypeHint === null) {
				self::assertNull($parameterTypeHint);
			} else {
				self::assertNotNull($parameterTypeHint);
				self::assertSame($expectedParameterTypeHint->getTypeHint(), $parameterTypeHint->getTypeHint());
				self::assertSame($expectedParameterTypeHint->isNullable(), $parameterTypeHint->isNullable());
				self::assertSame($expectedParameterTypeHint->isOptional(), $parameterTypeHint->isOptional());
			}
		}
		self::assertSame($expectedParametersWithoutTypeHints, FunctionHelper::getParametersWithoutTypeHint($codeSnifferFile, $functionPointer));
	}

	/**
	 * @dataProvider dataParametersNullableTypeHints
	 * @param string $functionName
	 * @param \SlevomatCodingStandard\Helpers\ParameterTypeHint[]|null[] $expectedParametersTypeHints
	 */
	public function testParametersNullableTypeHints(
		string $functionName,
		array $expectedParametersTypeHints
	): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionParametersNullableTypeHints.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, $functionName);
		$parametersTypeHints = FunctionHelper::getParametersTypeHints($codeSnifferFile, $functionPointer);
		foreach ($expectedParametersTypeHints as $expectedParameterName => $expectedParameterTypeHint) {
			self::assertArrayHasKey($expectedParameterName, $parametersTypeHints);
			$parameterTypeHint = $parametersTypeHints[$expectedParameterName];
			self::assertNotNull($parameterTypeHint);
			self::assertSame($expectedParameterTypeHint->getTypeHint(), $parameterTypeHint->getTypeHint(), $parameterTypeHint->getTypeHint());
			self::assertSame($expectedParameterTypeHint->isNullable(), $parameterTypeHint->isNullable(), $parameterTypeHint->getTypeHint());
			self::assertSame($expectedParameterTypeHint->isOptional(), $parameterTypeHint->isOptional(), $parameterTypeHint->getTypeHint());
		}
	}

	/**
	 * @return mixed[][]
	 */
	public function dataFunctionReturnsValueOrNot(): array
	{
		return [
			['returnsValue', true],
			['returnsVariable', true],
			['returnsValueInCondition', true],
			['returnsGenerator', true],
			['returnsGeneratorFrom', true],
			['returnsGeneratorWithEarlyTermination', true],
			['returnsGeneratorWithVeryEarlyTermination', true],
			['generatorIsInClosure', false],
			['noReturn', false],
			['returnsVoid', false],
			['containsClosure', false],
			['containsAnonymousClass', false],
		];
	}

	/**
	 * @dataProvider dataFunctionReturnsValueOrNot
	 * @param string $functionName
	 * @param bool $returnsValue
	 */
	public function testFunctionReturnsValueOrNot(
		string $functionName,
		bool $returnsValue
	): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionReturnsValueOrNot.php');
		self::assertSame($returnsValue, FunctionHelper::returnsValue($codeSnifferFile, $this->findFunctionPointerByName($codeSnifferFile, $functionName)));
	}

	/**
	 * @return mixed[][]
	 */
	public function dataClosureReturnsValueOrNot(): array
	{
		return [
			[3, true],
			[7, false],
			[11, true],
			[15, true],
		];
	}

	/**
	 * @dataProvider dataClosureReturnsValueOrNot
	 * @param int $line
	 * @param bool $returnsValue
	 */
	public function testClosureReturnsValueOrNot(
		int $line,
		bool $returnsValue
	): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/closureReturnsValueOrNot.php');
		self::assertSame($returnsValue, FunctionHelper::returnsValue($codeSnifferFile, $this->findPointerByLineAndType($codeSnifferFile, $line, T_CLOSURE)));
	}

	public function testReturnTypeHint(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionReturnTypeHint.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'withReturnTypeHint');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($codeSnifferFile, $functionPointer));
		$returnTypeHint = FunctionHelper::findReturnTypeHint($codeSnifferFile, $functionPointer);
		self::assertSame('\FooNamespace\FooInterface', $returnTypeHint->getTypeHint());
		self::assertFalse($returnTypeHint->isNullable());
		self::assertSame($functionPointer + 7, $returnTypeHint->getStartPointer());
		self::assertSame($functionPointer + 10, $returnTypeHint->getEndPointer());

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'withoutReturnTypeHint');
		self::assertFalse(FunctionHelper::hasReturnTypeHint($codeSnifferFile, $functionPointer));
		self::assertNull(FunctionHelper::findReturnTypeHint($codeSnifferFile, $functionPointer));

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'abstractWithReturnTypeHint');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($codeSnifferFile, $functionPointer));
		$returnTypeHint = FunctionHelper::findReturnTypeHint($codeSnifferFile, $functionPointer);
		self::assertSame('bool', $returnTypeHint->getTypeHint());
		self::assertFalse($returnTypeHint->isNullable());
		self::assertSame($functionPointer + 7, $returnTypeHint->getStartPointer());
		self::assertSame($functionPointer + 7, $returnTypeHint->getEndPointer());

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'abstractWithoutReturnTypeHint');
		self::assertFalse(FunctionHelper::hasReturnTypeHint($codeSnifferFile, $functionPointer));
		self::assertNull(FunctionHelper::findReturnTypeHint($codeSnifferFile, $functionPointer));
	}

	public function testReturnNullableTypeHint(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionReturnsNullableTypeHint.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'withReturnNullableTypeHint');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($codeSnifferFile, $functionPointer));
		$returnTypeHint = FunctionHelper::findReturnTypeHint($codeSnifferFile, $functionPointer);
		self::assertSame('\FooNamespace\FooInterface', $returnTypeHint->getTypeHint());
		self::assertTrue($returnTypeHint->isNullable());

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'abstractWithReturnNullableTypeHint');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($codeSnifferFile, $functionPointer));
		$returnTypeHint = FunctionHelper::findReturnTypeHint($codeSnifferFile, $functionPointer);
		self::assertSame('bool', $returnTypeHint->getTypeHint());
		self::assertTrue($returnTypeHint->isNullable());
	}

	public function testAnnotations(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionAnnotations.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'withAnnotations');

		$parametersAnnotations = array_map(function (Annotation $annotation) {
			return $annotation->getContent();
		}, FunctionHelper::getParametersAnnotations($codeSnifferFile, $functionPointer));
		self::assertSame([
			'string $a',
			'int $b',
		], $parametersAnnotations);
		self::assertSame('bool', FunctionHelper::findReturnAnnotation($codeSnifferFile, $functionPointer)->getContent());

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'withoutAnnotations');
		self::assertCount(0, FunctionHelper::getParametersAnnotations($codeSnifferFile, $functionPointer));
		self::assertNull(FunctionHelper::findReturnAnnotation($codeSnifferFile, $functionPointer));
	}

	public function testGetAllFunctionNames(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionNames.php');
		self::assertSame(['foo', 'boo'], FunctionHelper::getAllFunctionNames($codeSnifferFile));
	}

	public function testFindClassPointer(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionNames.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'foo');
		self::assertNull(FunctionHelper::findClassPointer($codeSnifferFile, $functionPointer));

		$closurePointer = $this->findPointerByLineAndType($codeSnifferFile, 9, T_CLOSURE);
		self::assertNull(FunctionHelper::findClassPointer($codeSnifferFile, $closurePointer));

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'doo');
		self::assertNotNull(FunctionHelper::findClassPointer($codeSnifferFile, $methodPointer));
	}

}
