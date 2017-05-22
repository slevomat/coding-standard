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
				'AnyNamespace\Traversable',
			],
		]);

		$this->assertSame(56, $report->getErrorCount());

		$this->assertSniffError($report, 9, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 16, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 23, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 30, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 37, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 44, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 120, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 126, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT, 'Method \FooClass::parametersWithoutTypeHintAndWithAnnotationWithoutParameterName() does not have parameter type hint for its parameter $a but it should be possible to add it based on @param annotation "string".');
		$this->assertSniffError($report, 126, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT, 'Method \FooClass::parametersWithoutTypeHintAndWithAnnotationWithoutParameterName() does not have parameter type hint for its parameter $b but it should be possible to add it based on @param annotation "bool|null".');
		$this->assertSniffError($report, 135, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 208, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);

		$this->assertSniffError($report, 48, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 56, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 64, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 72, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 80, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 88, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 112, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);

		$this->assertSniffError($report, 16, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 56, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 96, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 103, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 215, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 223, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 231, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 239, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 246, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 253, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);

		$this->assertSniffError($report, 105, TypeHintDeclarationSniff::CODE_MISSING_PROPERTY_TYPE_HINT);
		$this->assertSniffError($report, 107, TypeHintDeclarationSniff::CODE_MISSING_PROPERTY_TYPE_HINT);

		$this->assertSniffError($report, 139, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 144, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 149, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 154, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 162, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 200, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 200, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 267, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 275, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 312, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 325, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);

		$this->assertSniffError($report, 167, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 171, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 175, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 180, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 187, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 208, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 283, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 290, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 321, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 330, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);

		$this->assertSniffError($report, 192, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 195, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 297, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 302, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 307, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 336, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
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

	public function testEnabledEachParameterAndReturnInspectionNoErrors()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/typeHintDeclarationEnabledEachParameterAndReturnInspectionNoErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableEachParameterAndReturnInspection' => true,
		]));
	}

	public function testEnabledEachParameterAndReturnInspectionErrors()
	{
		$report = $this->checkFile(__DIR__ . '/data/typeHintDeclarationEnabledEachParameterAndReturnInspectionErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'usefulAnnotations' => ['@useful'],
			'enableEachParameterAndReturnInspection' => true,
		]);

		$this->assertSame(11, $report->getErrorCount());

		$parameterMessage = function (string $method, string $parameter): string {
			return sprintf('Method \FooNamespace\FooClass::%s() has useless @param annotation for parameter $%s.', $method, $parameter);
		};

		$this->assertSniffError($report, 11, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 18, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 27, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 35, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withUselessParameterButRequiredReturn', 'foo'));
		$this->assertSniffError($report, 45, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withMultipleUselessParametersAndReturn', 'foo'));
		$this->assertSniffError($report, 45, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withMultipleUselessParametersAndReturn', 'baz'));
		$this->assertSniffError($report, 45, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION);
		$this->assertSniffError($report, 53, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withDescriptionAndUselessParameter', 'foo'));
		$this->assertSniffError($report, 61, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION);
		$this->assertSniffError($report, 69, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withUsefulAnnotationAndUselessParameter', 'foo'));
		$this->assertSniffError($report, 77, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION);
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

	public function testFixableEnableEachParameterAndReturnInspection()
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableEnableEachParameterAndReturnInspection.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableEachParameterAndReturnInspection' => true,
			'usefulAnnotations' => ['@useful'],
		], [TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION]);

		$this->assertAllFixedInFile($report);
	}

}
