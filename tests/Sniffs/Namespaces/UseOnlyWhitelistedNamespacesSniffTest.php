<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class UseOnlyWhitelistedNamespacesSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testUseOnlyWhitelistedNamespaces(): void
	{
		$report = $this->checkFile(
			__DIR__ . '/data/whitelistedNamespacesInUses.php',
			['namespacesRequiredToUse' => [
				'Foo',
			]]
		);

		$this->assertSniffError(
			$report,
			5,
			UseOnlyWhitelistedNamespacesSniff::CODE_NON_FULLY_QUALIFIED,
			'Dolor'
		);
		$this->assertNoSniffError($report, 6);
		$this->assertSniffError(
			$report,
			7,
			UseOnlyWhitelistedNamespacesSniff::CODE_NON_FULLY_QUALIFIED,
			'Fooo\Baz'
		);
		$this->assertSniffError(
			$report,
			8,
			UseOnlyWhitelistedNamespacesSniff::CODE_NON_FULLY_QUALIFIED,
			'Lorem\Ipsum'
		);
	}

	public function testIgnoreUseFromAnonymousFunction(): void
	{
		$report = $this->checkFile(
			__DIR__ . '/data/whitelistedNamespacesInUses.php'
		);
		$this->assertNoSniffError($report, 12);
	}

	public function testIgnoreTraitUses(): void
	{
		$report = $this->checkFile(
			__DIR__ . '/data/whitelistedNamespacesInUses.php'
		);
		$this->assertNoSniffError($report, 19);
		$this->assertNoSniffError($report, 20);
		$this->assertNoSniffError($report, 21);
	}

	public function testAllowUseFromRootNamespace(): void
	{
		$report = $this->checkFile(
			__DIR__ . '/data/whitelistedNamespacesInUses.php',
			[
				'namespacesRequiredToUse' => [
					'Foo',
				],
				'allowUseFromRootNamespace' => true,
			]
		);

		$this->assertNoSniffError($report, 5);
		$this->assertNoSniffError($report, 6);
		$this->assertSniffError(
			$report,
			7,
			UseOnlyWhitelistedNamespacesSniff::CODE_NON_FULLY_QUALIFIED,
			'Fooo\Baz'
		);
		$this->assertSniffError(
			$report,
			8,
			UseOnlyWhitelistedNamespacesSniff::CODE_NON_FULLY_QUALIFIED,
			'Lorem\Ipsum'
		);
	}

}
