<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

class TypeHintDeclarationSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/typeHintDeclarationNoErrors.php', [
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
				'@Security',
			],
		]));
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
			'traversableTypeHints' => [
				\Traversable::class,
				'AnyNamespace\Traversable',
				'\Doctrine\Common\Collections\ArrayCollection',
			],
		]);

		self::assertSame(63, $report->getErrorCount());

		self::assertSniffError($report, 11, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 18, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 25, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 32, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 39, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 46, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 122, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 128, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT, 'Method \FooClass::parametersWithoutTypeHintAndWithAnnotationWithoutParameterName() does not have parameter type hint for its parameter $a but it should be possible to add it based on @param annotation "string".');
		self::assertSniffError($report, 128, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT, 'Method \FooClass::parametersWithoutTypeHintAndWithAnnotationWithoutParameterName() does not have parameter type hint for its parameter $b but it should be possible to add it based on @param annotation "bool|null".');
		self::assertSniffError($report, 137, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 210, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);

		self::assertSniffError($report, 50, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 58, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 66, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 74, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 82, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 90, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 114, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);

		self::assertSniffError($report, 18, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 58, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 98, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 105, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 217, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 225, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 233, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 241, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 248, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 255, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 386, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);

		self::assertSniffError($report, 107, TypeHintDeclarationSniff::CODE_MISSING_PROPERTY_TYPE_HINT);
		self::assertSniffError($report, 109, TypeHintDeclarationSniff::CODE_MISSING_PROPERTY_TYPE_HINT);

		self::assertSniffError($report, 141, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 146, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 151, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 154, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 162, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 200, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 267, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 275, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 312, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 327, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 354, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 375, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);

		self::assertSniffError($report, 169, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 173, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 177, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 180, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 187, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 208, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 283, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 290, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 321, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 332, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 346, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 367, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);

		self::assertSniffError($report, 193, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 196, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 297, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 302, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 307, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 337, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 341, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 362, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
	}

	public function testVoidAndIterable(): void
	{
		$report = self::checkFile(__DIR__ . '/data/voidAndIterableTypeHintDeclaration.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
			'traversableTypeHints' => [
				\Traversable::class,
			],
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 10, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 35, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
	}

	public function testEnabledVoidTypeHintNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/enabledVoidTypeHintNoErrors.php', [
			'enableVoidTypeHint' => true,
		]));
	}

	public function testEnabledVoidTypeHintErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/enabledVoidTypeHintErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => true,
			'enableObjectTypeHint' => false,
		]);

		self::assertSame(9, $report->getErrorCount());

		self::assertSniffError($report, 3, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 14, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 16, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 24, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 32, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 39, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 43, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 47, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 59, TypeHintDeclarationSniff::CODE_INCORRECT_RETURN_TYPE_HINT);
	}

	public function testEnabledNullableTypeHintsNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/typeHintDeclarationEnabledNullableTypeHintsNoErrors.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
		]));
	}

	public function testEnabledNullableTypeHintsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationEnabledNullableTypeHintsErrors.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
		]);

		self::assertSame(14, $report->getErrorCount());

		self::assertSniffError($report, 13, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 21, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 26, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 31, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 39, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 46, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 53, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 61, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 69, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 77, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 85, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 93, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 100, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 107, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
	}

	public function testDisabledNullableTypeHintsNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/typeHintDeclarationDisabledNullableTypeHintsNoErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
		]));
	}

	public function testDisabledNullableTypeHintsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationDisabledNullableTypeHintsErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 11, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 19, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 27, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
	}

	public function testEnabledEachParameterAndReturnInspectionNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/typeHintDeclarationEnabledEachParameterAndReturnInspectionNoErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
			'enableEachParameterAndReturnInspection' => true,
		]));
	}

	public function testEnabledEachParameterAndReturnInspectionErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationEnabledEachParameterAndReturnInspectionErrors.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
			'usefulAnnotations' => ['@useful'],
			'enableEachParameterAndReturnInspection' => true,
		]);

		self::assertSame(12, $report->getErrorCount());

		$parameterMessage = function (string $method, string $parameter): string {
			return sprintf('Method \FooNamespace\FooClass::%s() has useless @param annotation for parameter $%s.', $method, $parameter);
		};

		self::assertSniffError($report, 11, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 18, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 27, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 32, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withUselessParameterButRequiredReturn', 'foo'));
		self::assertSniffError($report, 40, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withMultipleUselessParametersAndReturn', 'foo'));
		self::assertSniffError($report, 42, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withMultipleUselessParametersAndReturn', 'baz'));
		self::assertSniffError($report, 43, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION);
		self::assertSniffError($report, 51, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withDescriptionAndUselessParameter', 'foo'));
		self::assertSniffError($report, 59, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION);
		self::assertSniffError($report, 67, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withUsefulAnnotationAndUselessParameter', 'foo'));
		self::assertSniffError($report, 75, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION);
		self::assertSniffError($report, 83, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, $parameterMessage('withInvalidParamAnnotation', 'foo'));
	}

	public function testFixableReturnTypeHints(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableReturnTypeHints.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
			'traversableTypeHints' => [
				\ArrayIterator::class,
				\Traversable::class,
			],
		], [TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT]);

		self::assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintsWithEnabledVoid(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableReturnTypeHintsWithEnabledVoid.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => true,
			'enableObjectTypeHint' => false,
		], [TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT, TypeHintDeclarationSniff::CODE_INCORRECT_RETURN_TYPE_HINT]);

		self::assertAllFixedInFile($report);
	}

	public function testFixableReturnTypeHintsWithDisabledVoid(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableReturnTypeHintsWithDisabledVoid.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
		], [TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT, TypeHintDeclarationSniff::CODE_INCORRECT_RETURN_TYPE_HINT]);

		self::assertAllFixedInFile($report);
	}

	public function testFixableParameterTypeHintsWithEnabledNullableTypeHints(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableParameterTypeHintsWithEnabledNullableTypeHints.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
			'traversableTypeHints' => [
				\ArrayIterator::class,
				\Traversable::class,
			],
		], [TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT]);

		self::assertAllFixedInFile($report);
	}

	public function testFixableParameterTypeHintsWithDisabledNullableTypeHints(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableParameterTypeHintsWithDisabledNullableTypeHints.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
		], [TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT]);

		self::assertAllFixedInFile($report);
	}

	public function testFixableUselessDocComments(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableUselessDocComments.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => true,
			'enableObjectTypeHint' => false,
		], [TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT]);

		self::assertAllFixedInFile($report);
	}

	public function testFixableEnableEachParameterAndReturnInspection(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableEnableEachParameterAndReturnInspection.php', [
			'enableNullableTypeHints' => false,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
			'enableEachParameterAndReturnInspection' => true,
			'usefulAnnotations' => ['@useful'],
		], [TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION]);

		self::assertAllFixedInFile($report);
	}

	public function testEnabledObjectTypeHintNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationEnabledObjectTypeHintNoErrors.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testEnabledObjectTypeHintErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationEnabledObjectTypeHintErrors.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => true,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 11, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 16, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
	}

	public function testDisabledObjectTypeHintNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationDisabledObjectTypeHintNoErrors.php', [
			'enableNullableTypeHints' => true,
			'enableVoidTypeHint' => false,
			'enableObjectTypeHint' => false,
		]);
		self::assertNoSniffErrorInFile($report);
	}

}
