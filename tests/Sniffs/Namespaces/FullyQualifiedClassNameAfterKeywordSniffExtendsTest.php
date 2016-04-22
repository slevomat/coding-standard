<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class FullyQualifiedClassNameAfterKeywordSniffExtendsTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	/**
	 * @return string
	 */
	protected function getSniffClassName()
	{
		return FullyQualifiedClassNameAfterKeywordSniff::class;
	}

	private function getFileReport()
	{
		return $this->checkFile(
			__DIR__ . '/data/fullyQualifiedExtends.php',
			['keywordsToCheck' => ['T_EXTENDS']]
		);
	}

	public function testNonFullyQualifiedExtends()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			8,
			FullyQualifiedClassNameAfterKeywordSniff::getErrorCode('extends'),
			'Bar'
		);
	}

	public function testFullyQualifiedExtends()
	{
		$this->assertNoSniffError($this->getFileReport(), 3);
	}

}
