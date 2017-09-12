<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class UseFromSameNamespaceSniffRootNamespaceTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	protected function getSniffClassName(): string
	{
		return UseFromSameNamespaceSniff::class;
	}

	public function testUseFromRootNamespaceInFileWithoutNamespace(): void
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
