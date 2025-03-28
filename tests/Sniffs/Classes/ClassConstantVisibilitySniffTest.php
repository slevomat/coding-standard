<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class ClassConstantVisibilitySniffTest extends TestCase
{

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classWithConstants.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertNoSniffError($report, 7);
		self::assertNoSniffError($report, 9);
		self::assertNoSniffError($report, 10);

		self::assertSniffError(
			$report,
			6,
			ClassConstantVisibilitySniff::CODE_MISSING_CONSTANT_VISIBILITY,
			'Constant \ClassWithConstants::PUBLIC_FOO visibility missing.',
		);

		self::assertSniffError(
			$report,
			13,
			ClassConstantVisibilitySniff::CODE_MISSING_CONSTANT_VISIBILITY,
			'Constant \ClassWithConstants::PUBLIC_INT_CONST visibility missing.',
		);

		self::assertSniffError(
			$report,
			25,
			ClassConstantVisibilitySniff::CODE_MISSING_CONSTANT_VISIBILITY,
			'Constant class@anonymous::PUBLIC_FOO visibility missing.',
		);

		self::assertSniffError(
			$report,
			27,
			ClassConstantVisibilitySniff::CODE_MISSING_CONSTANT_VISIBILITY,
			'Constant class@anonymous::FINAL_WITHOUT_VISIBILITY visibility missing.',
		);

		self::assertNoSniffError($report, 28);
		self::assertNoSniffError($report, 29);
	}

	public function testNoClassConstants(): void
	{
		$report = self::checkFile(__DIR__ . '/data/noClassConstants.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testNoClassConstantsWithNamespace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/noClassConstantsWithNamespace.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testFixableEnabled(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableMissingClassConstantVisibility.php',
			['fixable' => true],
			[ClassConstantVisibilitySniff::CODE_MISSING_CONSTANT_VISIBILITY],
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableDisabled(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableMissingClassConstantVisibilityFixableDisabled.php',
			['fixable' => false],
			[ClassConstantVisibilitySniff::CODE_MISSING_CONSTANT_VISIBILITY],
		);
		self::assertAllFixedInFile($report);
	}

}
