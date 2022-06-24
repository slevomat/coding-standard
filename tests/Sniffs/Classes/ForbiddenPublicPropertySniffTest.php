<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class ForbiddenPublicPropertySniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicPropertyNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicPropertyErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 5, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 6, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 7, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
	}

	public function testPromotedNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicPropertyPromotedNoErrors.php', [
			'checkPromoted' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testPromotedErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicPropertyPromotedErrors.php', [
			'checkPromoted' => true,
		]);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 5, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 13, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 14, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
	}

}
