<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class FullyQualifiedClassNameAfterKeywordSniffTest extends TestCase
{

	public function testThrowExceptionForUndefinedConstant(): void
	{
		try {
			self::checkFile(
				__DIR__ . '/data/fullyQualifiedExtends.php',
				['keywordsToCheck' => ['T_FOO']]
			);
			self::fail();
		} catch (UndefinedKeywordTokenException $e) {
			self::assertStringContainsString('T_FOO', $e->getMessage());
			self::assertSame('T_FOO', $e->getKeyword());
		}
	}

	public function testCheckNothingWhenNoKeywordsAreConfigured(): void
	{
		$fileReport = self::checkFile(__DIR__ . '/data/fullyQualifiedExtends.php');
		self::assertEmpty($fileReport->getErrors());
	}

	public function testFixableFullyQualified(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableFullyQualifiedClassNameAfterKeyword.php', [
			'keywordsToCheck' => [
				'T_EXTENDS',
				'T_IMPLEMENTS',
			],
		]);
		self::assertAllFixedInFile($report);
	}

}
