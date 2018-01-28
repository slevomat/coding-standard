<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

class TypeHintDeclarationSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/typeHintDeclarationNoErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
			'traversableTypeHints' => [
				\Traversable::class,
				'\QueryResultSet',
				'\FooNamespace\ClassFromCurrentNamespace',
				'\UsedNamespace\UsedClass',
				'\Doctrine\Common\Collections\ArrayCollection',
			],
			'usefulAnnotations' => [
				'@see',
				'@Assert\Callback',
				'@Something\\',
			],
		]));
	}

	public function testErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/typeHintDeclarationErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
			'traversableTypeHints' => [
				\Traversable::class,
				'AnyNamespace\Traversable',
				'\Doctrine\Common\Collections\ArrayCollection',
			],
		]);

		$this->assertSame(63, $report->getErrorCount());

		$this->assertSniffError($report, 11, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 18, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 25, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 32, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 39, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 46, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 122, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 128, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT, 'Method \FooClass::parametersWithoutTypeHintAndWithAnnotationWithoutParameterName() does not have parameter type hint for its parameter $a but it should be possible to add it based on @param annotation "string".');
		$this->assertSniffError($report, 128, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT, 'Method \FooClass::parametersWithoutTypeHintAndWithAnnotationWithoutParameterName() does not have parameter type hint for its parameter $b but it should be possible to add it based on @param annotation "bool|null".');
		$this->assertSniffError($report, 137, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 210, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);

		$this->assertSniffError($report, 50, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 58, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 66, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 74, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 82, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 90, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 114, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);

		$this->assertSniffError($report, 18, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 58, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 98, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 105, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 217, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 225, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 233, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 241, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 248, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 255, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 386, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);

		$this->assertSniffError($report, 107, TypeHintDeclarationSniff::CODE_MISSING_PROPERTY_TYPE_HINT);
		$this->assertSniffError($report, 109, TypeHintDeclarationSniff::CODE_MISSING_PROPERTY_TYPE_HINT);

		$this->assertSniffError($report, 141, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 146, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 151, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 154, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 162, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 200, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 267, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 275, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 312, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 327, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 354, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 375, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);

		$this->assertSniffError($report, 169, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 173, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 177, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 180, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 187, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 208, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 283, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 290, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 321, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 332, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 346, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 367, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);

		$this->assertSniffError($report, 193, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 196, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 297, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 302, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 307, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 337, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 341, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		$this->assertSniffError($report, 362, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
	}

	public function testVoidAndIterable(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/voidAndIterableTypeHintDeclaration.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
			'traversableTypeHints' => [
				\Traversable::class,
			],
		]);

		$this->assertSame(2, $report->getErrorCount());

		$this->assertSniffError($report, 10, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 35, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
	}

	public function testEnabledVoidTypeHintNoErrors(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/enabledVoidTypeHintNoErrors.php', [
			'enableVoidTypeHint' => true,
		]));
	}

	public function testEnabledVoidTypeHintErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/enabledVoidTypeHintErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => true,
			'enableObjectTypeHint' => false,
		]);

		$this->assertSame(8, $report->getErrorCount());

		$this->assertSniffError($report, 3, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 14, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 16, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 24, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 31, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 35, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 39, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 51, TypeHintDeclarationSniff::CODE_INCORRECT_RETURN_TYPE_HINT);
	}

	public function testEnabledNullableTypeHintsNoErrors(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/typeHintDeclarationEnabledNullableTypeHintsNoErrors.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
		]));
	}

	public function testEnabledNullableTypeHintsErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/typeHintDeclarationEnabledNullableTypeHintsErrors.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
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

	public function testDisabledNullableTypeHintsNoErrors(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/typeHintDeclarationDisabledNullableTypeHintsNoErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
		]));
	}

	public function testDisabledNullableTypeHintsErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/typeHintDeclarationDisabledNullableTypeHintsErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
		]);

		$this->assertSame(3, $report->getErrorCount());

		$this->assertSniffError($report, 11, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 19, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 27, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
	}

	public function testEnabledEachParameterAndReturnInspectionNoErrors(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/typeHintDeclarationEnabledEachParameterAndReturnInspectionNoErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
			'enableEachParameterAndReturnInspection' => true,
		]));
	}

	public function testEnabledEachParameterAndReturnInspectionErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/typeHintDeclarationEnabledEachParameterAndReturnInspectionErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
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
		$this->assertSniffError($report, 32, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withUselessParameterButRequiredReturn', 'foo'));
		$this->assertSniffError($report, 40, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withMultipleUselessParametersAndReturn', 'foo'));
		$this->assertSniffError($report, 42, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withMultipleUselessParametersAndReturn', 'baz'));
		$this->assertSniffError($report, 43, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION);
		$this->assertSniffError($report, 51, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withDescriptionAndUselessParameter', 'foo'));
		$this->assertSniffError($report, 59, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION);
		$this->assertSniffError($report, 67, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withUsefulAnnotationAndUselessParameter', 'foo'));
		$this->assertSniffError($report, 75, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION);
	}

	public function testFixableReturnTypeHints(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReturnTypeHints.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
			'traversableTypeHints' => [
				\ArrayIterator::class,
				\Traversable::class,
			],
		], [TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT]);

		$this->assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintsWithEnabledVoid(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReturnTypeHintsWithEnabledVoid.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => true,
			'enableObjectTypeHint' => false,
		], [TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT, TypeHintDeclarationSniff::CODE_INCORRECT_RETURN_TYPE_HINT]);

		$this->assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintsWithDisabledVoid(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableReturnTypeHintsWithDisabledVoid.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
		], [TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT, TypeHintDeclarationSniff::CODE_INCORRECT_RETURN_TYPE_HINT]);

		$this->assertAllFixedInFile($report);
	}

	public function testFixableParameterTypeHintsWithEnabledNullableTypeHints(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableParameterTypeHintsWithEnabledNullableTypeHints.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
			'traversableTypeHints' => [
				\ArrayIterator::class,
				\Traversable::class,
			],
		], [TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT]);

		$this->assertAllFixedInFile($report);
	}

	public function testFixableParameterTypeHintsWithDisabledNullableTypeHints(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableParameterTypeHintsWithDisabledNullableTypeHints.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
		], [TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT]);

		$this->assertAllFixedInFile($report);
	}

	public function testFixableUselessDocComments(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableUselessDocComments.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => true,
			'enableObjectTypeHint' => false,
		], [TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT]);

		$this->assertAllFixedInFile($report);
	}

	public function testFixableEnableEachParameterAndReturnInspection(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableEnableEachParameterAndReturnInspection.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
			'enableEachParameterAndReturnInspection' => true,
			'usefulAnnotations' => ['@useful'],
		], [TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION]);

		$this->assertAllFixedInFile($report);
	}

	public function testEnabledObjectTypeHintNoErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/typeHintDeclarationEnabledObjectTypeHintNoErrors.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => true,
		]);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testEnabledObjectTypeHintErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/typeHintDeclarationEnabledObjectTypeHintErrors.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => true,
		]);

		$this->assertSame(2, $report->getErrorCount());

		$this->assertSniffError($report, 11, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 16, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
	}

	public function testDisabledObjectTypeHintNoErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/typeHintDeclarationDisabledObjectTypeHintNoErrors.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
		]);
		$this->assertNoSniffErrorInFile($report);
	}

}
