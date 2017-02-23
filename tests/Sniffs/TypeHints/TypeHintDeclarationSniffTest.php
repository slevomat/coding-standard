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
				'@Assert\Callback',
				'@Something\\',
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

		$this->assertSame(46, $report->getErrorCount());

		$this->assertSniffError($report, 8, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 15, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 22, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 29, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 36, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 43, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 119, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 125, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT, 'Method \FooClass::parametersWithoutTypeHintAndWithAnnotationWithoutParameterName() does not have parameter type hint for its parameter $a but it should be possible to add it based on @param annotation "string".');
		$this->assertSniffError($report, 125, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT, 'Method \FooClass::parametersWithoutTypeHintAndWithAnnotationWithoutParameterName() does not have parameter type hint for its parameter $b but it should be possible to add it based on @param annotation "bool|null".');
		$this->assertSniffError($report, 134, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 207, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);

		$this->assertSniffError($report, 47, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 55, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 63, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 71, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 79, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 87, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 111, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);

		$this->assertSniffError($report, 95, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 102, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 214, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 222, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 230, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 238, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 245, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 252, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);

		$this->assertSniffError($report, 104, TypeHintDeclarationSniff::CODE_MISSING_PROPERTY_TYPE_HINT);
		$this->assertSniffError($report, 106, TypeHintDeclarationSniff::CODE_MISSING_PROPERTY_TYPE_HINT);

		$this->assertSniffError($report, 138, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 143, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 148, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 153, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 161, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 199, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 199, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 266, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 274, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);

		$this->assertSniffError($report, 166, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 170, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 174, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 179, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 186, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 207, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 282, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 289, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);

		$this->assertSniffError($report, 191, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 194, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
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

		$this->assertSame(14, $report->getErrorCount());

		$this->assertSniffError($report, 13, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 21, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 26, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 31, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 39, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 46, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 53, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 61, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 69, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 77, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 85, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 93, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 100, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 107, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
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
