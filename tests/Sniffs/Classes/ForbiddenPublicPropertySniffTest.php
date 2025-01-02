<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class ForbiddenPublicPropertySniffTest extends TestCase
{

	public function testDefault(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicProperty.php');

		self::assertSniffError($report, 5, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 6, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
	}

	public function testReadonly(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicPropertyReadonly.php');

		self::assertSniffError($report, 5, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 6, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 7, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 8, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 9, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
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
	}

	public function testReadonlyAllowed(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicPropertyReadonly.php', [
			'allowReadonly' => true,
		]);

		self::assertSniffError($report, 5, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 6, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 9, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
	}

	public function testReadonlyAllowedPromoted(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenPublicPropertyReadonly.php', [
			'allowReadonly' => true,
			'checkPromoted' => true,
		]);

		self::assertSniffError($report, 5, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 6, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 9, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
		self::assertSniffError($report, 12, ForbiddenPublicPropertySniff::CODE_FORBIDDEN_PUBLIC_PROPERTY);
	}

}
