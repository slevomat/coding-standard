<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class NullTypeHintOnLastPositionSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/nullTypeHintOnLastPositionNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/nullTypeHintOnLastPositionErrors.php');

		self::assertSame(25, $report->getErrorCount());

		self::assertSniffError($report, 7, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 11, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 15, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 19, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 27, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 35, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 43, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 53, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 54, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 55, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 56, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 57, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 58, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 68, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 71, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 74, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError($report, 77, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError(
			$report,
			80,
			NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION,
			'Null type hint should be on last position in "null|int".'
		);
		self::assertSniffError(
			$report,
			80,
			NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION,
			'Null type hint should be on last position in "null|bool".'
		);
		self::assertSniffError($report, 83, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError(
			$report,
			92,
			NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION,
			'Null type hint should be on last position in "null|bool".'
		);
		self::assertSniffError(
			$report,
			92,
			NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION,
			'Null type hint should be on last position in "null|int".'
		);

		self::assertSniffError(
			$report,
			101,
			NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION,
			'Null type hint should be on last position in "null|int".'
		);
		self::assertSniffError(
			$report,
			101,
			NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION,
			'Null type hint should be on last position in "null|bool".'
		);
		self::assertSniffError($report, 104, NullTypeHintOnLastPositionSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);

		self::assertAllFixedInFile($report);
	}

}
