<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Complexity;

use SlevomatCodingStandard\Sniffs\TestCase;
use function sprintf;

class CognitiveSniffTest extends TestCase
{

	/**
	 * @return list<array{0: string, 1: int, 2: string, 3: int}>
	 */
	public static function dataProviderFiles(): array
	{
		return [
			[
				__DIR__ . '/data/cognitive/boolOperatorChains.php',
				3,
				'binaryLogicalOperators',
				19,
			],
			[
				__DIR__ . '/data/cognitive/closureIncNesting.php',
				3,
				'myMethod2',
				2,
			],
			[
				__DIR__ . '/data/cognitive/continue.php',
				3,
				'sumOfPrimes',
				6,
			],
			[
				__DIR__ . '/data/cognitive/interface.php',
				5,
				'overriddenSymbolFrom',
				0,
			],
			[
				__DIR__ . '/data/cognitive/nesting.php',
				3,
				'someFunction',
				9,
			],
			[
				__DIR__ . '/data/cognitive/nesting2.php',
				3,
				'overriddenSymbolFrom',
				19,
			],
			[
				__DIR__ . '/data/cognitive/nestingContinueWithLabel.php',
				3,
				'sumOfPrimes',
				7,
			],
			[
				__DIR__ . '/data/cognitive/nestingElseifNoInc.php',
				3,
				'testHybrid',
				8,
			],
			[
				__DIR__ . '/data/cognitive/nestingIncrements.php',
				3,
				'nestingIncrements',
				5,
			],
			[
				__DIR__ . '/data/cognitive/switch.php',
				3,
				'switchCaseTest',
				9,
			],
			[
				__DIR__ . '/data/cognitive/switchMissingBreak.php',
				6,
				'caseWithoutBreak',
				5,
			],
			[
				__DIR__ . '/data/cognitive/ternary.php',
				3,
				'ternaryTest',
				3,
			],
			[
				__DIR__ . '/data/cognitive/doWhile.php',
				3,
				'doWhile',
				9,
			],
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	 * @dataProvider dataProviderFiles
	 */
	public function testNoErrorsOrWarnings(string $filepath, int $line, string $functionName, int $expectedComplexity): void
	{
		$report = self::checkFile($filepath, [
			'errorThreshold' => $expectedComplexity + 1,
			'warningThreshold' => $expectedComplexity + 1,
		]);
		self::assertNoSniffErrorInFile($report);
		self::assertNoSniffWarningInFile($report);
	}

	/**
	 * @dataProvider dataProviderFiles
	 */
	public function testWarnings(string $filepath, int $line, string $functionName, int $expectedComplexity): void
	{
		$report = self::checkFile($filepath, [
			'errorThreshold' => $expectedComplexity + 1,
			'warningThreshold' => $expectedComplexity,
		]);

		self::assertSame(1, $report->getWarningCount());
		self::assertNoSniffErrorInFile($report);
		self::assertSniffWarning(
			$report,
			$line,
			CognitiveSniff::CODE_COMPLEXITY,
			sprintf(
				'Cognitive complexity for "%s" is %s but has to be less than or equal to %s.',
				$functionName,
				$expectedComplexity,
				$expectedComplexity - 1,
			),
		);
	}

	/**
	 * @dataProvider dataProviderFiles
	 */
	public function testErrors(string $filepath, int $line, string $functionName, int $expectedComplexity): void
	{
		$report = self::checkFile($filepath, [
			'errorThreshold' => $expectedComplexity,
			'warningThreshold' => $expectedComplexity - 1,
		]);

		self::assertSame(1, $report->getErrorCount());
		self::assertNoSniffWarningInFile($report);
		self::assertSniffError(
			$report,
			$line,
			CognitiveSniff::CODE_COMPLEXITY,
			sprintf(
				'Cognitive complexity for "%s" is %s but has to be less than or equal to %s.',
				$functionName,
				$expectedComplexity,
				$expectedComplexity - 2,
			),
		);
	}

	public function testErrorAndWarning(): void
	{
		$filepath = __DIR__ . '/data/cognitive/warnAndError.php';
		$warnInfo = [
			'complexity' => 6,
			'func' => 'warning',
			'line' => 3,
		];
		$errorInfo = [
			'complexity' => 9,
			'func' => 'error',
			'line' => 15,
		];

		$report = self::checkFile($filepath, [
			'errorThreshold' => 9,
			'warningThreshold' => 6,
		]);

		self::assertSame(1, $report->getWarningCount());
		self::assertSame(1, $report->getErrorCount());
		self::assertSniffWarning(
			$report,
			$warnInfo['line'],
			CognitiveSniff::CODE_COMPLEXITY,
			sprintf(
				'Cognitive complexity for "%s" is %s but has to be less than or equal to %s.',
				$warnInfo['func'],
				$warnInfo['complexity'],
				5,
			),
		);
		self::assertSniffError(
			$report,
			$errorInfo['line'],
			CognitiveSniff::CODE_COMPLEXITY,
			sprintf(
				'Cognitive complexity for "%s" is %s but has to be less than or equal to %s.',
				$errorInfo['func'],
				$errorInfo['complexity'],
				5,
			),
		);
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	 * @dataProvider dataProviderFiles
	 */
	public function testDeprecatedNoErrorsOrWarnings(string $filepath, int $line, string $functionName, int $expectedComplexity): void
	{
		$report = self::checkFile($filepath, [
			'maxComplexity' => $expectedComplexity,
		]);
		self::assertNoSniffErrorInFile($report);
		self::assertNoSniffWarningInFile($report);
	}

	/**
	 * @dataProvider dataProviderFiles
	 */
	public function testDeprecatedErrors(string $filepath, int $line, string $functionName, int $expectedComplexity): void
	{
		$report = self::checkFile($filepath, [
			'maxComplexity' => $expectedComplexity - 1,
		]);

		self::assertSame(1, $report->getErrorCount());
		self::assertNoSniffWarningInFile($report);
		self::assertSniffError(
			$report,
			$line,
			CognitiveSniff::CODE_COMPLEXITY,
			sprintf(
				'Cognitive complexity for "%s" is %s but has to be less than or equal to %s.',
				$functionName,
				$expectedComplexity,
				$expectedComplexity - 1,
			),
		);
	}

}
