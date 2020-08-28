<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use SlevomatCodingStandard\Helpers\Annotation\Annotation;
use function array_map;
use const PHP_VERSION_ID;
use const T_CLOSURE;

class FunctionHelperTest extends TestCase
{

	public function testNameWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionWithNamespace.php');
		self::assertSame(
			'\FooNamespace\fooFunction',
			FunctionHelper::getFullyQualifiedName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooFunction'))
		);
		self::assertSame('fooFunction', FunctionHelper::getName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooFunction')));
		self::assertSame(
			'\FooNamespace\FooClass::fooMethod',
			FunctionHelper::getFullyQualifiedName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooMethod'))
		);
		self::assertSame('fooMethod', FunctionHelper::getName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooMethod')));
	}

	public function testNameWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionWithoutNamespace.php');
		self::assertSame(
			'fooFunction',
			FunctionHelper::getFullyQualifiedName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooFunction'))
		);
		self::assertSame('fooFunction', FunctionHelper::getName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooFunction')));
		self::assertSame(
			'\FooClass::fooMethod',
			FunctionHelper::getFullyQualifiedName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooMethod'))
		);
		self::assertSame('fooMethod', FunctionHelper::getName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooMethod')));
	}

	public function testNameInAnonymousClass(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionInAnonymousClass.php');
		self::assertSame(
			'class@anonymous::fooMethod',
			FunctionHelper::getFullyQualifiedName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooMethod'))
		);
		self::assertSame('fooMethod', FunctionHelper::getName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooMethod')));
	}

	public function testAbstractOrNot(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionAbstractOrNot.php');
		self::assertFalse(FunctionHelper::isAbstract($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooMethod')));
		self::assertTrue(FunctionHelper::isAbstract($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooAbstractMethod')));
		self::assertTrue(FunctionHelper::isAbstract($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooInterfaceMethod')));
	}

	public function testFunctionOrMethod(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionOrMethod.php');
		self::assertTrue(FunctionHelper::isMethod($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooMethod')));
		self::assertFalse(FunctionHelper::isMethod($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooFunction')));
		self::assertFalse(FunctionHelper::isMethod($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooFunctionInCondition')));
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
	public function testParametersNames(string $functionName, array $expectedParametersNames): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionParametersNames.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, $functionName);
		self::assertSame($expectedParametersNames, FunctionHelper::getParametersNames($phpcsFile, $functionPointer));
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
	 * @param (ParameterTypeHint|null)[] $expectedParametersTypeHints
	 */
	public function testParametersTypeHints(string $functionName, array $expectedParametersTypeHints): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionParametersTypeHints.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, $functionName);
		$parametersTypeHints = FunctionHelper::getParametersTypeHints($phpcsFile, $functionPointer);
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
	}

	/**
	 * @dataProvider dataParametersNullableTypeHints
	 * @param string $functionName
	 * @param (ParameterTypeHint|null)[] $expectedParametersTypeHints
	 */
	public function testParametersNullableTypeHints(string $functionName, array $expectedParametersTypeHints): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionParametersNullableTypeHints.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, $functionName);
		$parametersTypeHints = FunctionHelper::getParametersTypeHints($phpcsFile, $functionPointer);
		foreach ($expectedParametersTypeHints as $expectedParameterName => $expectedParameterTypeHint) {
			self::assertArrayHasKey($expectedParameterName, $parametersTypeHints);
			$parameterTypeHint = $parametersTypeHints[$expectedParameterName];
			self::assertNotNull($parameterTypeHint);
			self::assertSame(
				$expectedParameterTypeHint->getTypeHint(),
				$parameterTypeHint->getTypeHint(),
				$parameterTypeHint->getTypeHint()
			);
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
	public function testFunctionReturnsValueOrNot(string $functionName, bool $returnsValue): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionReturnsValueOrNot.php');
		self::assertSame(
			$returnsValue,
			FunctionHelper::returnsValue($phpcsFile, $this->findFunctionPointerByName($phpcsFile, $functionName))
		);
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
	public function testClosureReturnsValueOrNot(int $line, bool $returnsValue): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/closureReturnsValueOrNot.php');
		self::assertSame(
			$returnsValue,
			FunctionHelper::returnsValue($phpcsFile, $this->findPointerByLineAndType($phpcsFile, $line, T_CLOSURE))
		);
	}

	public function testReturnTypeHint(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionReturnTypeHint.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'withReturnTypeHint');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		self::assertSame('\FooNamespace\FooInterface', $returnTypeHint->getTypeHint());
		self::assertFalse($returnTypeHint->isNullable());

		// Names are one token in PHP 8
		if (PHP_VERSION_ID < 80000) {
			self::assertSame($functionPointer + 7, $returnTypeHint->getStartPointer());
			self::assertSame($functionPointer + 10, $returnTypeHint->getEndPointer());
		}

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'withReturnTypeHintNoSpace');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		self::assertSame('\FooNamespace\FooInterface', $returnTypeHint->getTypeHint());
		self::assertFalse($returnTypeHint->isNullable());

		// Names are one token in PHP 8
		if (PHP_VERSION_ID < 80000) {
			self::assertSame($functionPointer + 7, $returnTypeHint->getStartPointer());
			self::assertSame($functionPointer + 10, $returnTypeHint->getEndPointer());
		}

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'withoutReturnTypeHint');
		self::assertFalse(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));
		self::assertNull(FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer));

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'abstractWithReturnTypeHint');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));

		/** @var ReturnTypeHint $returnTypeHint */
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		self::assertSame('bool', $returnTypeHint->getTypeHint());
		self::assertFalse($returnTypeHint->isNullable());

		// Names are one token in PHP 8
		if (PHP_VERSION_ID < 80000) {
			self::assertSame($functionPointer + 7, $returnTypeHint->getStartPointer());
			self::assertSame($functionPointer + 7, $returnTypeHint->getEndPointer());
		}

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'abstractWithoutReturnTypeHint');
		self::assertFalse(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));
		self::assertNull(FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer));
	}

	public function testReturnNullableTypeHint(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionReturnsNullableTypeHint.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'withReturnNullableTypeHint');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		self::assertSame('\FooNamespace\FooInterface', $returnTypeHint->getTypeHint());
		self::assertTrue($returnTypeHint->isNullable());

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'abstractWithReturnNullableTypeHint');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		self::assertSame('bool', $returnTypeHint->getTypeHint());
		self::assertTrue($returnTypeHint->isNullable());
	}

	public function testAnnotations(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionAnnotations.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'withAnnotations');

		$parametersAnnotations = array_map(static function (Annotation $annotation): ?string {
			return $annotation->getContent();
		}, FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer));
		self::assertSame([
			'string $a',
			'int $b',
		], $parametersAnnotations);
		self::assertSame('bool', FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer)->getContent());

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'withoutAnnotations');
		self::assertCount(0, FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer));
		self::assertNull(FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer));
	}

	public function testGetAllFunctionNames(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionNames.php');
		self::assertSame(['foo', 'boo'], FunctionHelper::getAllFunctionNames($phpcsFile));
	}

	public function testFindClassPointer(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionNames.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'foo');
		self::assertNull(FunctionHelper::findClassPointer($phpcsFile, $functionPointer));

		$closurePointer = $this->findPointerByLineAndType($phpcsFile, 9, T_CLOSURE);
		self::assertNull(FunctionHelper::findClassPointer($phpcsFile, $closurePointer));

		$methodPointer = $this->findFunctionPointerByName($phpcsFile, 'doo');
		self::assertNotNull(FunctionHelper::findClassPointer($phpcsFile, $methodPointer));
	}

}
