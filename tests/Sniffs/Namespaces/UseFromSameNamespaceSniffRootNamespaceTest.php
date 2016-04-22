<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class UseFromSameNamespaceSniffRootNamespaceTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	/**
	 * @return string
	 */
	protected function getSniffClassName()
	{
		return UseFromSameNamespaceSniff::class;
	}

	public function testUseFromRootNamespaceInFileWithoutNamespace()
	{
		$report = $this->checkFile(__DIR__ . '/data/useFromRootNamespaceWithoutNamespace.php');
		$this->assertSniffError(
			$report,
			3,
			UseFromSameNamespaceSniff::CODE_USE_FROM_SAME_NAMESPACE,
			'Foo'
		);
		$this->assertNoSniffError($report, 4);
	}

}
