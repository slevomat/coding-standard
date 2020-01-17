<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Sniffs\TestCase;

class FullyQualifiedClassNameAfterKeywordSniffExtendsTest extends TestCase
{

	public function testNonFullyQualifiedExtends(): void
	{
		self::assertSniffError(
			$this->getFileReport(),
			8,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('extends'),
			'Bar'
		);
	}

	public function testFullyQualifiedExtends(): void
	{
		self::assertNoSniffError($this->getFileReport(), 3);
	}

	protected static function getSniffClassName(): string
	{
		return FullyQualifiedClassNameAfterKeywordSniff::class;
	}

	private function getFileReport(): File
	{
		return self::checkFile(
			__DIR__ . '/data/fullyQualifiedExtends.php',
			['keywordsToCheck' => ['T_EXTENDS']]
		);
	}

}
