<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use SlevomatCodingStandard\Sniffs\TestCase;

class ReferenceSpacingSniffTest extends TestCase
{

	public function testDefaultSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/referenceSpacingDefaultSettingsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/referenceSpacingDefaultSettingsErrors.php');

		self::assertSame(12, $report->getErrorCount());

		self::assertSniffError($report, 3, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 8, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 13, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 17, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 22, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 26, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 28, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 29, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 30, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 33, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 36, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 36, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/referenceSpacingModifiedSettingsNoErrors.php', [
			'spacesCountAfterReference' => 2,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/referenceSpacingModifiedSettingsErrors.php', [
			'spacesCountAfterReference' => 2,
		]);

		self::assertSame(10, $report->getErrorCount());

		self::assertSniffError($report, 3, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 8, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 13, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 17, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 22, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 26, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 28, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 29, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 30, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);
		self::assertSniffError($report, 33, ReferenceSpacingSniff::CODE_INCORRECT_SPACES_AFTER_REFERENCE);

		self::assertAllFixedInFile($report);
	}

}
