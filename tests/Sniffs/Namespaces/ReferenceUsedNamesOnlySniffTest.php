<?php

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class ReferenceUsedNamesOnlySniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testDoNotReportNamespaceName()
	{
		$report = $this->checkFile(__DIR__ . '/data/shouldBeInUseStatement.php');
		$this->assertNoSniffError($report, 3);
	}

	public function testCreatingNewObjectViaNonFullyQualifiedName()
	{
		$report = $this->checkFile(__DIR__ . '/data/shouldBeInUseStatement.php');
		$this->assertNoSniffError($report, 10);
	}

	public function testCreatingNewObjectViaFullyQualifiedName()
	{
		$report = $this->checkFile(__DIR__ . '/data/shouldBeInUseStatement.php');
		$this->assertSniffError(
			$report,
			12,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Foo\SomeError'
		);
	}

	public function testReferencingConstantViaFullyQualifiedName()
	{
		$report = $this->checkFile(__DIR__ . '/data/shouldBeInUseStatement.php');
		$this->assertSniffError(
			$report,
			11,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Some\ConstantClass'
		);
	}

	public function testCreatingObjectFromSpecialExceptionName()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/shouldBeInUseStatement.php',
			[
				'allowFullyQualifiedExceptions' => true,
				'specialExceptionNames' => [
					'Foo\SomeError',
				],
			]
		);
		$this->assertNoSniffError($report, 12);
	}

	public function testDoNotAllowFullyQualifiedExtends()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/fullyQualifiedExtends.php'
		);
		$this->assertSniffError(
			$report,
			3,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Lorem\Dolor'
		);
		$this->assertNoSniffError($report, 8);
	}

	public function testDoNotAllowFullyQualifiedImplements()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/fullyQualifiedImplements.php'
		);
		$this->assertSniffError(
			$report,
			3,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE,
			'\SomeClass'
		);
		$this->assertSniffError(
			$report,
			3,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Some\OtherClass'
		);
		$this->assertNoSniffError($report, 8);
	}

	public function testAllowFullyQualifiedExceptions()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/fullyQualifiedExceptionNames.php',
			['allowFullyQualifiedExceptions' => true]
		);
		$this->assertSame(1, $report->getErrorCount());
		$this->assertSniffError(
			$report,
			28,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Foo\BarError'
		);
	}

	public function testDoNotAllowFullyQualifiedExceptionsInTypeHint()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/fullyQualifiedExceptionNames.php',
			['allowFullyQualifiedExceptions' => false]
		);
		$this->assertSniffError(
			$report,
			16,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Some\Exception'
		);
		$this->assertNoSniffError($report, 3);
	}

	public function testDoNotAllowFullyQualifiedExceptionsInThrow()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/fullyQualifiedExceptionNames.php',
			['allowFullyQualifiedExceptions' => false]
		);
		$this->assertSniffError(
			$report,
			19,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Some\Other\Exception'
		);
		$this->assertNoSniffError($report, 6);
	}

	public function testDoNotAllowFullyQualifiedExceptionsInCatch()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/fullyQualifiedExceptionNames.php',
			['allowFullyQualifiedExceptions' => false]
		);
		$this->assertSniffError(
			$report,
			20,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Some\Other\DifferentException'
		);
		$this->assertSniffError(
			$report,
			22,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Throwable'
		);
		$this->assertSniffError(
			$report,
			24,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Exception'
		);
		$this->assertSniffError(
			$report,
			26,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\TypeError'
		);
		$this->assertSniffError(
			$report,
			28,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Foo\BarError'
		);
		$this->assertNoSniffError($report, 7);
		$this->assertNoSniffError($report, 9);
		$this->assertNoSniffError($report, 11);
	}

	public function testDoNotAllowPartialUses()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/partialUses.php',
			[
				'allowPartialUses' => false,
			]
		);
		$this->assertSniffError(
			$report,
			7,
			ReferenceUsedNamesOnlySniff::CODE_PARTIAL_USE,
			'SomeFramework\Object'
		);
		$this->assertNoSniffError($report, 6);
	}

	public function testAllowPartialUses()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/partialUses.php',
			[
				'allowPartialUses' => true,
			]
		);
		$this->assertNoSniffError($report, 6);
		$this->assertNoSniffError($report, 7);
	}

	public function testUseOnlyWhitelistedNamespaces()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/whitelistedNamespaces.php',
			['namespacesRequiredToUse' => [
				'Foo',
			]]
		);

		$this->assertSniffError(
			$report,
			3,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Foo\Bar'
		);
		$this->assertNoSniffError($report, 4);
		$this->assertNoSniffError($report, 5);
	}

	public function testAllowFullyQualifiedImplementsWithMultipleInterfaces()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/multipleFullyQualifiedImplements.php',
			[
				'fullyQualifiedKeywords' => ['T_IMPLEMENTS'],
			]
		);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testDisallowFullyQualifiedImplementsWithMultipleInterfaces()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/multipleFullyQualifiedImplements.php'
		);
		$this->assertSniffError(
			$report,
			3,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE,
			'\Bar'
		);
		$this->assertSniffError(
			$report,
			3,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE,
			'\Baz'
		);
		$this->assertSniffError(
			$report,
			3,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Foo\Baz'
		);
	}

	public function testDoNotUseTypeInRootNamespaceInFileWithoutNamespace()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/referencingFullyQualifiedNameInFileWithoutNamespace.php'
		);

		$this->assertSniffError(
			$report,
			3,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE,
			'\Foo'
		);
		$this->assertSniffError(
			$report,
			4,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Bar\Lorem'
		);
	}

}
