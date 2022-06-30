<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class BackedEnumTypeSpacingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/backedEnumTypeSpacingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/backedEnumTypeSpacingErrors.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 3, BackedEnumTypeSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON);
		self::assertSniffError($report, 3, BackedEnumTypeSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_TYPE);
		self::assertSniffError($report, 8, BackedEnumTypeSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON);
		self::assertSniffError($report, 8, BackedEnumTypeSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_TYPE);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/backedEnumTypeSpacingModifiedSettingsNoErrors.php', [
			'spacesCountBeforeColon' => 2,
			'spacesCountBeforeType' => 0,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/backedEnumTypeSpacingModifiedSettingsErrors.php', [
			'spacesCountBeforeColon' => 2,
			'spacesCountBeforeType' => 0,
		]);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 3, BackedEnumTypeSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON);
		self::assertSniffError($report, 3, BackedEnumTypeSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_TYPE);
		self::assertSniffError($report, 8, BackedEnumTypeSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_COLON);
		self::assertSniffError($report, 8, BackedEnumTypeSpacingSniff::CODE_INCORRECT_SPACES_BEFORE_TYPE);

		self::assertAllFixedInFile($report);
	}

}
