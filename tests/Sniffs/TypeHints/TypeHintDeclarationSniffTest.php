<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use ArrayIterator;
use SlevomatCodingStandard\Sniffs\TestCase;
use Traversable;
use function sprintf;

class TypeHintDeclarationSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationNoErrors.php', [
			'enableObjectTypeHint' => false,
			'traversableTypeHints' => [
				Traversable::class,
				'\QueryResultSet',
				'\FooNamespace\ClassFromCurrentNamespace',
				'\UsedNamespace\UsedClass',
				'\Doctrine\Common\Collections\ArrayCollection',
			],
			'allAnnotationsAreUseful' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationErrors.php', [
			'enableObjectTypeHint' => false,
			'traversableTypeHints' => [
				Traversable::class,
				'AnyNamespace\Traversable',
				'\Doctrine\Common\Collections\ArrayCollection',
			],
		]);

		self::assertSame(72, $report->getErrorCount());

		self::assertSniffError($report, 11, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 18, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 25, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 32, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 39, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 46, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 122, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 193, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);

		self::assertSniffError($report, 50, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 58, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 66, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 74, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 82, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 90, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 114, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 185, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 297, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 376, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 381, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 388, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 412, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 417, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 424, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 428, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 432, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);

		self::assertSniffError($report, 436, TypeHintDeclarationSniff::CODE_INCORRECT_RETURN_TYPE_HINT);
		self::assertSniffError($report, 440, TypeHintDeclarationSniff::CODE_INCORRECT_RETURN_TYPE_HINT);

		self::assertSniffError($report, 18, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 58, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 98, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 105, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 200, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 208, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 216, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 224, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 231, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 238, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 369, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);

		self::assertSniffError($report, 107, TypeHintDeclarationSniff::CODE_MISSING_PROPERTY_TYPE_HINT);
		self::assertSniffError($report, 109, TypeHintDeclarationSniff::CODE_MISSING_PROPERTY_TYPE_HINT);

		self::assertSniffError($report, 124, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 129, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 134, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 137, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 145, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 183, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 250, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 258, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 295, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 310, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 337, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 358, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION);

		self::assertSniffError($report, 152, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 156, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 160, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 163, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 170, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 191, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 266, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 273, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 304, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 315, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 329, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 350, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION);

		self::assertSniffError($report, 176, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 179, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 280, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 285, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 290, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 324, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 324, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 345, TypeHintDeclarationSniff::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION);
	}

	public function testVoidAndIterable(): void
	{
		$report = self::checkFile(__DIR__ . '/data/voidAndIterableTypeHintDeclaration.php', [
			'enableObjectTypeHint' => false,
			'traversableTypeHints' => [
				Traversable::class,
			],
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 10, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
		self::assertSniffError($report, 10, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 35, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
	}

	public function testNullableTypeHintsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationNullableTypeHintsNoErrors.php', [
			'enableObjectTypeHint' => false,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNullableTypeHintsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationNullableTypeHintsErrors.php', [
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

	public function testEnabledEachParameterAndReturnInspectionNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/typeHintDeclarationEnabledEachParameterAndReturnInspectionNoErrors.php', [
			'enableObjectTypeHint' => false,
			'enableEachParameterAndReturnInspection' => true,
		]));
	}

	public function testEnabledEachParameterAndReturnInspectionErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationEnabledEachParameterAndReturnInspectionErrors.php', [
			'enableObjectTypeHint' => false,
			'allAnnotationsAreUseful' => true,
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
			'enableObjectTypeHint' => false,
			'traversableTypeHints' => [
				ArrayIterator::class,
				Traversable::class,
			],
		], [TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT, TypeHintDeclarationSniff::CODE_INCORRECT_RETURN_TYPE_HINT]);

		self::assertAllFixedInFile($report);
	}

	public function testFixableParameterTypeHintsWithNullableTypeHints(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableParameterTypeHintsWithNullableTypeHints.php', [
			'enableObjectTypeHint' => false,
			'traversableTypeHints' => [
				ArrayIterator::class,
				Traversable::class,
			],
		], [TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT]);

		self::assertAllFixedInFile($report);
	}

	public function testFixableUselessDocComments(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableUselessDocComments.php', [
			'enableObjectTypeHint' => false,
		], [TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT]);

		self::assertAllFixedInFile($report);
	}

	public function testFixableEnableEachParameterAndReturnInspection(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableEnableEachParameterAndReturnInspection.php', [
			'enableObjectTypeHint' => false,
			'enableEachParameterAndReturnInspection' => true,
			'allAnnotationsAreUseful' => true,
		], [TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION]);

		self::assertAllFixedInFile($report);
	}

	public function testEnabledObjectTypeHintNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationEnabledObjectTypeHintNoErrors.php', [
			'enableObjectTypeHint' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testEnabledObjectTypeHintErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationEnabledObjectTypeHintErrors.php', [
			'enableObjectTypeHint' => true,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 11, TypeHintDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT);
		self::assertSniffError($report, 16, TypeHintDeclarationSniff::CODE_MISSING_PARAMETER_TYPE_HINT);
	}

	public function testDisabledObjectTypeHintNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationDisabledObjectTypeHintNoErrors.php', [
			'enableObjectTypeHint' => false,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testAllAnnotationsAreUseful(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationAllAnnotationsAreUseful.php', [
			'allAnnotationsAreUseful' => true,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 9, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 19, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);

		self::assertAllFixedInFile($report);
	}

	public function testAllAnnotationsAreUsefulAndEachParameterAndReturnInspectionEnabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/typeHintDeclarationAllAnnotationsAreUsefulAndEachParameterAndReturnInspectionEnabled.php', [
			'allAnnotationsAreUseful' => true,
			'enableEachParameterAndReturnInspection' => true,
		]);

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError($report, 9, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 19, TypeHintDeclarationSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 26, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION);
		self::assertSniffError($report, 34, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION);
		self::assertSniffError($report, 35, TypeHintDeclarationSniff::CODE_USELESS_PARAMETER_ANNOTATION);
		self::assertSniffError($report, 36, TypeHintDeclarationSniff::CODE_USELESS_RETURN_ANNOTATION);

		self::assertAllFixedInFile($report);
	}

}
