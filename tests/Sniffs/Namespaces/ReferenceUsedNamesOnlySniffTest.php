<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class ReferenceUsedNamesOnlySniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	/**
	 * @return mixed[][]
	 */
	public function dataIgnoredNamesForIrrelevantTests(): array
	{
		return [
			[
				[],
			],
			[
				['LibXMLError'],
			],
		];
	}

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testDoNotReportNamespaceName(array $ignoredNames)
	{
		$report = $this->checkFile(__DIR__ . '/data/shouldBeInUseStatement.php', [
			'ignoredNames' => $ignoredNames,
		]);
		$this->assertNoSniffError($report, 3);
	}

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testCreatingNewObjectViaNonFullyQualifiedName(array $ignoredNames)
	{
		$report = $this->checkFile(__DIR__ . '/data/shouldBeInUseStatement.php', [
			'ignoredNames' => $ignoredNames,
		]);
		$this->assertNoSniffError($report, 10);
	}

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testCreatingNewObjectViaFullyQualifiedName(array $ignoredNames)
	{
		$report = $this->checkFile(__DIR__ . '/data/shouldBeInUseStatement.php', [
			'ignoredNames' => $ignoredNames,
		]);
		$this->assertSniffError(
			$report,
			12,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Foo\SomeError'
		);
	}

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testReferencingConstantViaFullyQualifiedName(array $ignoredNames)
	{
		$report = $this->checkFile(__DIR__ . '/data/shouldBeInUseStatement.php', [
			'ignoredNames' => $ignoredNames,
		]);
		$this->assertSniffError(
			$report,
			11,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Some\ConstantClass'
		);
	}

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testCreatingObjectFromSpecialExceptionName(array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/shouldBeInUseStatement.php',
			[
				'allowFullyQualifiedExceptions' => true,
				'specialExceptionNames' => [
					'Foo\SomeError',
				],
				'ignoredNames' => $ignoredNames,
			]
		);
		$this->assertNoSniffError($report, 12);
	}

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testReportFullyQualifiedInFileWithNamespace(array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/shouldBeInUseStatement.php',
			[
				'ignoredNames' => $ignoredNames,
			]
		);
		$this->assertSniffError(
			$report,
			13,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Some\CommonException'
		);
		$this->assertSniffError(
			$report,
			14,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Exception'
		);
		$this->assertSniffError(
			$report,
			15,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Nette\Object'
		);
	}

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testDoNotAllowFullyQualifiedExtends(array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/fullyQualifiedExtends.php',
			[
				'ignoredNames' => $ignoredNames,
			]
		);
		$this->assertSniffError(
			$report,
			3,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Lorem\Dolor'
		);
		$this->assertNoSniffError($report, 8);
	}

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testDoNotAllowFullyQualifiedImplements(array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/fullyQualifiedImplements.php',
			[
				'ignoredNames' => $ignoredNames,
			]
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

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testAllowFullyQualifiedExceptions(array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/fullyQualifiedExceptionNames.php',
			[
				'allowFullyQualifiedExceptions' => true,
				'ignoredNames' => $ignoredNames,
			]
		);
		$this->assertSame(1, $report->getErrorCount());
		$this->assertSniffError(
			$report,
			28,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Foo\BarError'
		);
	}

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testDoNotAllowFullyQualifiedExceptionsInTypeHint(array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/fullyQualifiedExceptionNames.php',
			[
				'allowFullyQualifiedExceptions' => false,
				'ignoredNames' => $ignoredNames,
			]
		);
		$this->assertSniffError(
			$report,
			16,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Some\Exception'
		);
		$this->assertNoSniffError($report, 3);
	}

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testDoNotAllowFullyQualifiedExceptionsInThrow(array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/fullyQualifiedExceptionNames.php',
			[
				'allowFullyQualifiedExceptions' => false,
				'ignoredNames' => $ignoredNames,
			]
		);
		$this->assertSniffError(
			$report,
			19,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME,
			'\Some\Other\Exception'
		);
		$this->assertNoSniffError($report, 6);
	}

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testDoNotAllowFullyQualifiedExceptionsInCatch(array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/fullyQualifiedExceptionNames.php',
			[
				'allowFullyQualifiedExceptions' => false,
				'ignoredNames' => $ignoredNames,
			]
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
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE,
			'\Throwable'
		);
		$this->assertSniffError(
			$report,
			24,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE,
			'\Exception'
		);
		$this->assertSniffError(
			$report,
			26,
			ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE,
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

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testDoNotAllowPartialUses(array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/partialUses.php',
			[
				'allowPartialUses' => false,
				'ignoredNames' => $ignoredNames,
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

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testAllowPartialUses(array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/partialUses.php',
			[
				'allowPartialUses' => true,
				'ignoredNames' => $ignoredNames,
			]
		);
		$this->assertNoSniffError($report, 6);
		$this->assertNoSniffError($report, 7);
	}

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testUseOnlyWhitelistedNamespaces(array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/whitelistedNamespaces.php',
			[
				'namespacesRequiredToUse' => [
					'Foo',
				],
				'ignoredNames' => $ignoredNames,
			]
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

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testAllowFullyQualifiedImplementsWithMultipleInterfaces(array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/multipleFullyQualifiedImplements.php',
			[
				'fullyQualifiedKeywords' => ['T_IMPLEMENTS'],
				'ignoredNames' => $ignoredNames,
			]
		);
		$this->assertNoSniffErrorInFile($report);
	}

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testDisallowFullyQualifiedImplementsWithMultipleInterfaces(array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/multipleFullyQualifiedImplements.php',
			[
				'ignoredNames' => $ignoredNames,
			]
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

	/**
	 * @dataProvider dataIgnoredNamesForIrrelevantTests
	 * @param string[] $ignoredNames
	 */
	public function testDoNotUseTypeInRootNamespaceInFileWithoutNamespace(array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/referencingFullyQualifiedNameInFileWithoutNamespace.php',
			[
				'ignoredNames' => $ignoredNames,
			]
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

	/**
	 * @return mixed[][]
	 */
	public function dataIgnoredNames(): array
	{
		return [
			[
				false,
				[],
			],
			[
				true,
				[
					'LibXMLError',
					'LibXMLException',
				],
			],
		];
	}

	/**
	 * @dataProvider dataIgnoredNames
	 * @param bool $allowFullyQualifiedExceptions
	 * @param string[] $ignoredNames
	 */
	public function testIgnoredNames(bool $allowFullyQualifiedExceptions, array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/ignoredNames.php',
			[
				'allowFullyQualifiedExceptions' => $allowFullyQualifiedExceptions,
				'ignoredNames' => $ignoredNames,
			]
		);
		$this->assertNoSniffError($report, 3);
		$this->assertNoSniffError($report, 7);
		$this->assertSniffError($report, 11, ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE);
		$this->assertSniffError($report, 15, ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE);
	}

	public function testIgnoredNamesWithAllowFullyQualifiedExceptions()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/ignoredNames.php',
			['allowFullyQualifiedExceptions' => true]
		);
		$this->assertNoSniffErrorInFile($report);
	}

	/**
	 * @return mixed[][]
	 */
	public function dataIgnoredNamesInNamespace(): array
	{
		return [
			[
				false,
				[],
			],
			[
				true,
				[
					'LibXMLError',
					'LibXMLException',
				],
			],
		];
	}

	/**
	 * @dataProvider dataIgnoredNamesInNamespace
	 * @param bool $allowFullyQualifiedExceptions
	 * @param string[] $ignoredNames
	 */
	public function testIgnoredNamesInNamespace(bool $allowFullyQualifiedExceptions, array $ignoredNames)
	{
		$report = $this->checkFile(
			__DIR__ . '/data/ignoredNamesInNamespace.php',
			[
				'allowFullyQualifiedExceptions' => $allowFullyQualifiedExceptions,
				'ignoredNames' => $ignoredNames,
			]
		);
		$this->assertNoSniffError($report, 5);
		$this->assertNoSniffError($report, 9);
		$this->assertSniffError($report, 13, ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME);
		$this->assertSniffError($report, 17, ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME);
	}

	public function testIgnoredNamesWithAllowFullyQualifiedExceptionsInNamespace()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/ignoredNamesInNamespace.php',
			['allowFullyQualifiedExceptions' => true]
		);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testThrowExceptionForUndefinedKeyword()
	{
		$this->expectException(\SlevomatCodingStandard\Sniffs\Namespaces\UndefinedKeywordTokenException::class);
		$this->expectExceptionMessage('Value for keyword token not found, constant "T_FOO" is not defined');

		$this->checkFile(
			__DIR__ . '/data/unknownKeyword.php',
			['fullyQualifiedKeywords' => ['T_FOO']]
		);
	}

	public function testAllowingFullyQualifiedGlobalTypes()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/fullyQualifiedGlobalClassesInNamespace.php',
			[
				'allowFullyQualifiedGlobalClasses' => true,
			]
		);
		$this->assertNoSniffErrorInFile($report);
	}


	public function testFixableReferenceViaFullyQualifiedName()
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReferenceViaFullyQualifiedName.php', [
			'fullyQualifiedKeywords' => ['T_EXTENDS'],
			'allowFullyQualifiedExceptions' => true,
		], [ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableReferenceViaFullyQualifiedNameNoUseStatements()
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReferenceViaFullyQualifiedNameNoUseStatements.php', [
			'fullyQualifiedKeywords' => ['T_EXTENDS', 'T_IMPLEMENTS'],
		], [ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME]);
		$this->assertAllFixedInFile($report);
	}

	public function testFixableReferenceViaFullyQualifiedNameWithoutNamespace()
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReferenceViaFullyQualifiedNameWithoutNamespace.php', [
			'fullyQualifiedKeywords' => ['T_IMPLEMENTS'],
			'allowFullyQualifiedExceptions' => false,
			'specialExceptionNames' => [
				'BarErrorX',
			],
		], [ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE]);
		$this->assertAllFixedInFile($report);
	}

	public function testCollidingClassNameDifferentNamespacesAllowed()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/collidingClassNameDifferentNamespaces.php',
			['allowFullyQualifiedNameForCollidingClasses' => true]
		);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testCollidingClassNameDifferentNamespacesDisallowed()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/collidingClassNameDifferentNamespaces.php',
			['allowFullyQualifiedNameForCollidingClasses' => false]
		);

		$this->assertSame(2, $report->getErrorCount());

		$this->assertSniffError($report, 14, ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME);
		$this->assertSniffError($report, 16, ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME);
	}

	public function testCollidingClassNameDifferentNamespacesMoreClassesAllowed()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/collidingClassNameDifferentNamespacesMoreClasses.php',
			['allowFullyQualifiedNameForCollidingClasses' => true]
		);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testCollidingClassNameDifferentNamespacesMoreClassesDisallowed()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/collidingClassNameDifferentNamespacesMoreClasses.php',
			['allowFullyQualifiedNameForCollidingClasses' => false]
		);

		$this->assertSame(4, $report->getErrorCount());

		$this->assertSniffError($report, 7, ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME);
		$this->assertSniffError($report, 9, ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME);
		$this->assertSniffError($report, 12, ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME);
		$this->assertSniffError($report, 14, ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME);
	}

	public function testCollidingClassNameExtendsAllowed()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/collidingClassNameExtends.php',
			['allowFullyQualifiedNameForCollidingClasses' => true]
		);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testCollidingClassNameExtendsDisabled()
	{
		$report = $this->checkFile(
			__DIR__ . '/data/collidingClassNameExtends.php',
			['allowFullyQualifiedNameForCollidingClasses' => false]
		);
		$this->assertSame(1, $report->getErrorCount());
		$this->assertSniffError($report, 5, ReferenceUsedNamesOnlySniff::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME);
	}

}
