<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class ForbiddenPublicPropertySniffTest extends TestCase
{

	public function testDefault(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicProperty.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertNoSniffError($report, 18);
		self::assertNoSniffError($report, 19);
		self::assertNoSniffError($report, 22);
		self::assertNoSniffError($report, 23);

		self::assertSniffError($report, 16, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 17, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 20, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
	}

	public function testReadonly(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicPropertyReadonly.php');

		self::assertSniffError($report, 5, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 6, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 7, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 8, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 9, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertNoSniffError($report, 12);
		self::assertNoSniffError($report, 13);
		self::assertNoSniffError($report, 21);
	}

	public function testReadonlyPromoted(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicPropertyReadonly.php', [
			'checkPromoted' => true,
		]);

		self::assertSniffError($report, 5, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 6, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 7, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 8, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 9, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 12, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 13, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertNoSniffError($report, 21);
	}

	public function testReadonlyAllowed(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicPropertyReadonly.php', [
			'allowReadonly' => true,
		]);

		self::assertSniffError($report, 5, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 6, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertNoSniffError($report, 7);
		self::assertNoSniffError($report, 8);
		self::assertSniffError($report, 9, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertNoSniffError($report, 12);
		self::assertNoSniffError($report, 13);
		self::assertNoSniffError($report, 21);
	}

	public function testReadonlyAllowedPromoted(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicPropertyReadonly.php', [
			'allowReadonly' => true,
			'checkPromoted' => true,
		]);

		self::assertSniffError($report, 5, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 6, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertNoSniffError($report, 7);
		self::assertNoSniffError($report, 8);
		self::assertSniffError($report, 9, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 12, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertNoSniffError($report, 13);
		self::assertNoSniffError($report, 21);
	}

}
