<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Complexity;

use SlevomatCodingStandard\Sniffs\TestCase;
use function sprintf;

class CognitiveSniffTest extends TestCase
{

	/**
	 * @return mixed[][]
	 */
	public function dataProviderFiles(): array
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
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	 * @dataProvider dataProviderFiles
	 */
	public function testNoErrors(string $filepath, int $line, string $functionName, int $expectedComplexity): void
	{
		$report = self::checkFile($filepath, [
			'maxComplexity' => $expectedComplexity,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	/**
	 * @dataProvider dataProviderFiles
	 */
	public function testErrors(string $filepath, int $line, string $functionName, int $expectedComplexity): void
	{
		$maxComplexity = $expectedComplexity - 1;
		$report = self::checkFile($filepath, [
			'maxComplexity' => $maxComplexity,
		]);

		self::assertSame(1, $report->getErrorCount());
		self::assertSniffError(
			$report,
			$line,
			CognitiveSniff::CODE_COMPLEXITY,
			sprintf(
				'Cognitive complexity for "%s" is %s but has to be less than or equal to %s.',
				$functionName,
				$expectedComplexity,
				$maxComplexity
			)
		);
	}

}
