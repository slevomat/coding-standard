<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

class TypeHintDeclarationSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/typeHintDeclarationNoErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'traversableTypeHints' => [
				\Traversable::class,
				'\QueryResultSet',
				'\FooNamespace\ClassFromCurrentNamespace',
				'\UsedNamespace\UsedClass',
			],
			'usefulAnnotations' => [
				'@see',
			],
		]));
	}

	public function testErrors()
	{
		$report = $this->checkFile(__DIR__ . '/data/typeHintDeclarationErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'traversableTypeHints' => [
				\Traversable::class,
			],
		]);

		$this->assertSame(21, $report->getErrorCount());

		$this->assertSniffError($report, 6, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 13, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 20, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 27, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 34, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 41, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 117, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 123, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT, 'Method \FooClass::parametersWithoutTypeHintAndWithAnnotationWithoutParameterName() does not have parameter type hint for its parameter $a but it should be possible to add it based on @param annotation "string".');
		$this->assertSniffError($report, 123, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT, 'Method \FooClass::parametersWithoutTypeHintAndWithAnnotationWithoutParameterName() does not have parameter type hint for its parameter $b but it should be possible to add it based on @param annotation "bool|null".');
		$this->assertSniffError($report, 132, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);

		$this->assertSniffError($report, 45, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 53, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 61, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 69, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 77, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 85, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 109, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);

		$this->assertSniffError($report, 93, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 100, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);

		$this->assertSniffError($report, 102, TypeHintDeclarationSniff::CODE_MISSING_PROPERTY_TYPE_HINT);
		$this->assertSniffError($report, 104, TypeHintDeclarationSniff::CODE_MISSING_PROPERTY_TYPE_HINT);
	}

	public function testVoidAndIterable()
	{
		$report = $this->checkFile(__DIR__ . '/data/voidAndIterableTypeHintDeclaration.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'traversableTypeHints' => [
				\Traversable::class,
			],
		]);

		$this->assertSame(2, $report->getErrorCount());

		$this->assertSniffError($report, 10, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 35, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
	}

	public function testEnabledVoidTypeHintNoErrors()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/enabledVoidTypeHintNoErrors.php', [
			'enableVoidTypeHint' => true,
		]));
	}

	public function testEnabledVoidTypeHintErrors()
	{
		$report = $this->checkFile(__DIR__ . '/data/enabledVoidTypeHintErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => true,
		]);

		$this->assertSame(4, $report->getErrorCount());

		$this->assertSniffError($report, 3, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 14, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 16, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 24, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
	}

	public function testEnabledNullableTypeHintsNoErrors()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/typeHintDeclarationEnabledNullableTypeHintsNoErrors.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
		]));
	}

	public function testEnabledNullableTypeHintsErrors()
	{
		$report = $this->checkFile(__DIR__ . '/data/typeHintDeclarationEnabledNullableTypeHintsErrors.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
		]);

		$this->assertSame(8, $report->getErrorCount());

		$this->assertSniffError($report, 11, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 19, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 24, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 29, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 37, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 44, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 51, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 59, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
	}

	public function testDisabledNullableTypeHintsNoErrors()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/typeHintDeclarationDisabledNullableTypeHintsNoErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
		]));
	}

	public function testDisabledNullableTypeHintsErrors()
	{
		$report = $this->checkFile(__DIR__ . '/data/typeHintDeclarationDisabledNullableTypeHintsErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
		]);

		$this->assertSame(3, $report->getErrorCount());

		$this->assertSniffError($report, 11, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 19, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 27, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
	}

	public function testFixableReturnTypeHints()
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReturnTypeHints.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'traversableTypeHints' => [
				\ArrayIterator::class,
				\Traversable::class,
			],
		], [TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT]);

		$this->assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintsWithEnabledVoid()
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReturnTypeHintsWithEnabledVoid.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => true,
		], [TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT]);

		$this->assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintsWithDisabledVoid()
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReturnTypeHintsWithDisabledVoid.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
		], [TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT]);

		$this->assertAllFixedInFile($report);
	}

	public function testFixableParameterTypeHintsWithEnabledNullableTypeHints()
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableParameterTypeHintsWithEnabledNullableTypeHints.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'traversableTypeHints' => [
				\ArrayIterator::class,
				\Traversable::class,
			],
		], [TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT]);

		$this->assertAllFixedInFile($report);
	}

	public function testFixableParameterTypeHintsWithDisabledNullableTypeHints()
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableParameterTypeHintsWithDisabledNullableTypeHints.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
		], [TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT]);

		$this->assertAllFixedInFile($report);
	}

	public function testFixableUselessDocComments()
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableUselessDocComments.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => true,
		], [TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT]);

		$this->assertAllFixedInFile($report);
	}

}
