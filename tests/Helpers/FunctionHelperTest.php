<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use function array_map;
use function sprintf;
use const T_CLOSURE;

class FunctionHelperTest extends TestCase
{

	public function testNameWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionWithNamespace.php');
		self::assertSame(
			'\FooNamespace\fooFunction',
			FunctionHelper::getFullyQualifiedName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooFunction')),
		);
		self::assertSame('fooFunction', FunctionHelper::getName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooFunction')));
		self::assertSame(
			'\FooNamespace\FooClass::fooMethod',
			FunctionHelper::getFullyQualifiedName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooMethod')),
		);
		self::assertSame('fooMethod', FunctionHelper::getName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooMethod')));
	}

	public function testNameWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionWithoutNamespace.php');
		self::assertSame(
			'fooFunction',
			FunctionHelper::getFullyQualifiedName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooFunction')),
		);
		self::assertSame('fooFunction', FunctionHelper::getName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooFunction')));
		self::assertSame(
			'\FooClass::fooMethod',
			FunctionHelper::getFullyQualifiedName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooMethod')),
		);
		self::assertSame('fooMethod', FunctionHelper::getName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooMethod')));
	}

	public function testNameInAnonymousClass(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionInAnonymousClass.php');
		self::assertSame(
			'class@anonymous::fooMethod',
			FunctionHelper::getFullyQualifiedName($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooMethod')),
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
		self::assertFalse(FunctionHelper::isMethod($phpcsFile, $this->findFunctionPointerByName($phpcsFile, 'fooFunctionInFooMethod')));
	}

	/**
	 * @return list<array{0: string, 1: list<string>}>
	 */
	public static function dataParametersNames(): array
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
	 * @param list<string> $expectedParametersNames
	 */
	public function testParametersNames(string $functionName, array $expectedParametersNames): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionParametersNames.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, $functionName);
		self::assertSame($expectedParametersNames, FunctionHelper::getParametersNames($phpcsFile, $functionPointer));
	}

	/**
	 * @return list<array{0: string, 1: array<string, TypeHint|null>}>
	 */
	public static function dataParametersTypeHints(): array
	{
		return [
			[
				'allParametersWithTypeHints',
				[
					'$string' => new TypeHint('string', false, 0, 0),
					'$int' => new TypeHint('int', false, 0, 0),
					'$bool' => new TypeHint('bool', false, 0, 0),
					'$float' => new TypeHint('float', false, 0, 0),
					'$callable' => new TypeHint('callable', false, 0, 0),
					'$array' => new TypeHint('array', false, 0, 0),
					'$object' => new TypeHint('\FooNamespace\FooClass', false, 0, 0),
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
					'$string' => new TypeHint('string', false, 0, 0),
					'$int' => null,
					'$bool' => new TypeHint('bool', false, 0, 0),
					'$float' => null,
					'$callable' => new TypeHint('callable', false, 0, 0),
					'$array' => null,
					'$object' => new TypeHint('\FooNamespace\FooClass', false, 0, 0),
				],
			],
			[
				'parametersWithWeirdDefinition',
				[
					'$string' => new TypeHint('string', false, 0, 0),
					'$int' => null,
					'$bool' => new TypeHint('bool', false, 0, 0),
					'$float' => null,
					'$callable' => new TypeHint('callable', false, 0, 0),
					'$array' => null,
					'$object' => new TypeHint('\FooNamespace\FooClass', false, 0, 0),
				],
			],
			[
				'unionTypeHints',
				[
					'$a' => new TypeHint('string|int', false, 0, 0),
					'$b' => new TypeHint('int|false', false, 0, 0),
					'$c' => new TypeHint('null|int', true, 0, 0),
					'$d' => new TypeHint('string|int|float', false, 0, 0),
				],
			],
			[
				'dnf',
				[
					'$a' => new TypeHint('(A&B)|C|D', false, 0, 0),
					'$b' => new TypeHint('A|(B&C)|D', false, 0, 0),
					'$c' => new TypeHint('A|B|(C&D)', false, 0, 0),
				],
			],
		];
	}

	/**
	 * @return list<array{0: string, 1: array<string, TypeHint|null>}>
	 */
	public static function dataParametersNullableTypeHints(): array
	{
		return [
			[
				'allParametersWithNullableTypeHints',
				[
					'$string' => new TypeHint('?string', true, 0, 0),
					'$int' => new TypeHint('?int', true, 0, 0),
					'$bool' => new TypeHint('?bool', true, 0, 0),
					'$float' => new TypeHint('?float', true, 0, 0),
					'$callable' => new TypeHint('?callable', true, 0, 0),
					'$array' => new TypeHint('?array', true, 0, 0),
					'$object' => new TypeHint('?\FooNamespace\FooClass', true, 0, 0),
				],
			],
			[
				'someParametersWithNullableTypeHints',
				[
					'$string' => new TypeHint('?string', true, 0, 0),
					'$int' => new TypeHint('int', false, 0, 0),
					'$bool' => new TypeHint('?bool', true, 0, 0),
					'$float' => new TypeHint('float', false, 0, 0),
					'$callable' => new TypeHint('?callable', true, 0, 0),
					'$array' => new TypeHint('array', false, 0, 0),
					'$object' => new TypeHint('?\FooNamespace\FooClass', true, 0, 0),
				],
			],
			[
				'parametersWithWeirdDefinition',
				[
					'$string' => new TypeHint('?string', true, 0, 0),
					'$int' => new TypeHint('int', false, 0, 0),
					'$bool' => new TypeHint('?bool', true, 0, 0),
					'$float' => new TypeHint('float', false, 0, 0),
					'$callable' => new TypeHint('?callable', true, 0, 0),
					'$array' => new TypeHint('array', false, 0, 0),
					'$object' => new TypeHint('?\FooNamespace\FooClass', true, 0, 0),
				],
			],
		];
	}

	/**
	 * @dataProvider dataParametersTypeHints
	 * @param array<string, TypeHint|null> $expectedParametersTypeHints
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
				self::assertSame(
					$expectedParameterTypeHint->getTypeHint(),
					$parameterTypeHint->getTypeHint(),
					sprintf('Parameter %s', $expectedParameterName),
				);
				self::assertSame(
					$expectedParameterTypeHint->isNullable(),
					$parameterTypeHint->isNullable(),
					sprintf('Nullable parameter %s', $expectedParameterName),
				);
			}
		}
	}

	/**
	 * @dataProvider dataParametersNullableTypeHints
	 * @param array<string, TypeHint|null> $expectedParametersTypeHints
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
				$parameterTypeHint->getTypeHint(),
			);
			self::assertSame($expectedParameterTypeHint->isNullable(), $parameterTypeHint->isNullable(), $parameterTypeHint->getTypeHint());
		}
	}

	/**
	 * @return list<array{0: string, 1: bool}>
	 */
	public static function dataFunctionReturnsValueOrNot(): array
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
	 */
	public function testFunctionReturnsValueOrNot(string $functionName, bool $returnsValue): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionReturnsValueOrNot.php');
		self::assertSame(
			$returnsValue,
			FunctionHelper::returnsValue($phpcsFile, $this->findFunctionPointerByName($phpcsFile, $functionName)),
		);
	}

	/**
	 * @return list<array{0: int, 1: bool}>
	 */
	public static function dataClosureReturnsValueOrNot(): array
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
	 */
	public function testClosureReturnsValueOrNot(int $line, bool $returnsValue): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/closureReturnsValueOrNot.php');
		self::assertSame(
			$returnsValue,
			FunctionHelper::returnsValue($phpcsFile, $this->findPointerByLineAndType($phpcsFile, $line, T_CLOSURE)),
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
		self::assertSame($functionPointer + 7, $returnTypeHint->getStartPointer());
		self::assertSame($functionPointer + 10, $returnTypeHint->getEndPointer());

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'withReturnTypeHintNoSpace');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		self::assertSame('\FooNamespace\FooInterface', $returnTypeHint->getTypeHint());
		self::assertFalse($returnTypeHint->isNullable());
		self::assertSame($functionPointer + 7, $returnTypeHint->getStartPointer());
		self::assertSame($functionPointer + 10, $returnTypeHint->getEndPointer());

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'withoutReturnTypeHint');
		self::assertFalse(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));
		self::assertNull(FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer));

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'abstractWithReturnTypeHint');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));

		/** @var TypeHint $returnTypeHint */
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		self::assertSame('bool', $returnTypeHint->getTypeHint());
		self::assertFalse($returnTypeHint->isNullable());
		self::assertSame($functionPointer + 7, $returnTypeHint->getStartPointer());
		self::assertSame($functionPointer + 7, $returnTypeHint->getEndPointer());

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'abstractWithoutReturnTypeHint');
		self::assertFalse(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));
		self::assertNull(FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer));

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'unionReturnTypeHint');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		self::assertSame('string|int', $returnTypeHint->getTypeHint());

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'unionReturnTypeHintWithWhitespace');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		self::assertSame('string|int|bool', $returnTypeHint->getTypeHint());
	}

	public function testReturnNullableTypeHint(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionReturnsNullableTypeHint.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'withReturnNullableTypeHint');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		self::assertSame('?\FooNamespace\FooInterface', $returnTypeHint->getTypeHint());
		self::assertTrue($returnTypeHint->isNullable());

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'abstractWithReturnNullableTypeHint');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		self::assertSame('?bool', $returnTypeHint->getTypeHint());
		self::assertTrue($returnTypeHint->isNullable());

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'unionWithNull');
		self::assertTrue(FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer));
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		self::assertSame('null|string', $returnTypeHint->getTypeHint());
		self::assertTrue($returnTypeHint->isNullable());
	}

	public function testAnnotations(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionAnnotations.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'withAnnotations');

		$parametersAnnotations = array_map(
			static fn (Annotation $annotation): string => (string) $annotation->getValue(),
			FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer),
		);
		self::assertSame([
			'string $a',
			'int $b',
		], $parametersAnnotations);
		self::assertSame('bool', (string) FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer)->getValue());

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'withoutAnnotations');
		self::assertCount(0, FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer));
		self::assertNull(FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer));
	}

	public function testGetAllFunctionNames(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionNames.php');
		self::assertSame(['foo', 'boo'], FunctionHelper::getAllFunctionNames($phpcsFile));
	}

	public function testGetFunctionLengthInLines(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/functionLength.php');
		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'countMe');

		self::assertSame(2, FunctionHelper::getFunctionLengthInLines($phpcsFile, $functionPointer));
		self::assertSame(7, FunctionHelper::getFunctionLengthInLines(
			$phpcsFile,
			$functionPointer,
			FunctionHelper::LINE_INCLUDE_COMMENT,
		));
		self::assertSame(3, FunctionHelper::getFunctionLengthInLines(
			$phpcsFile,
			$functionPointer,
			FunctionHelper::LINE_INCLUDE_WHITESPACE,
		));
		self::assertSame(8, FunctionHelper::getFunctionLengthInLines(
			$phpcsFile,
			$functionPointer,
			FunctionHelper::LINE_INCLUDE_COMMENT | FunctionHelper::LINE_INCLUDE_WHITESPACE,
		));
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
