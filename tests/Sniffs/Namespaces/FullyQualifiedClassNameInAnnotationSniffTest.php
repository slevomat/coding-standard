<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class FullyQualifiedClassNameInAnnotationSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fullyQualifiedClassNameInAnnotationNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fullyQualifiedClassNameInAnnotationErrors.php');

		self::assertSame(89, $report->getErrorCount());

		self::assertSniffError(
			$report,
			20,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\PropertySameNamespace in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			23,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\PropertyUsed in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			26,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\Partial\PropertyPartiallyUsed in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			33,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\Foo in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			38,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\Foo in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			42,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\Bar in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			42,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\Foo in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			45,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\Foo in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			57,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\Foo in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			62,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\VariableSameNamespace in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			68,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\VariableWithCommentSameNamespace in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			76,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\ParamSameNamespace in @param should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			78,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\ReturnSameNamespace in @return should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			86,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\Partial in @param should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			96,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @property should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			97,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @property-read should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			98,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @property-write should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			99,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			99,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Traversable in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			99,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Exception in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			100,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			100,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			101,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			101,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			101,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTime in @method should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			109,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @property should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			109,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @property should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			114,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			114,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			117,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			117,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Traversable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			117,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			120,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			120,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTime in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			123,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			123,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTime in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			126,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			129,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			129,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTime in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			132,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			135,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			135,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Traversable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			135,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @var should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			144,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @return should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			144,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Traversable in @return should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			153,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			156,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @var should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			160,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			161,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			161,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Traversable in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			162,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Constant name \SORT_DESC in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			162,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Constant name \SORT_NUMERIC in @method should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			170,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTime in @template should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			171,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\DateTimeInterface in @template should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			172,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @template should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			173,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @template-covariant should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			174,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @template-extends should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			174,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @template-extends should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			175,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @template-implements should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			175,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @template-implements should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			176,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @template-use should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			176,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @template-use should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			182,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\TemplateThatDoesNotExist in @phpstan-return should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			192,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\PropertyUsed in @mixin should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			203,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @param should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			210,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @param should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			217,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @param should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			217,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTime in @param should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			249,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\Partial\Conditional1 in @return should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			249,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\Partial\Conditional2 in @return should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			249,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\Partial\Conditional3 in @return should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			249,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\Partial\Conditional4 in @return should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			249,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\Partial\Conditional5 in @return should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			249,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\Partial\Conditional6 in @return should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			249,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\Partial\Conditional7 in @return should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			256,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\Partial\Conditional8 in @return should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			256,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\Partial\Conditional9 in @return should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			256,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\Partial\Conditional10 in @return should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			268,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTime in @param-out should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			280,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTime in @phpstan-self-out should be referenced via a fully qualified name'
		);

		self::assertAllFixedInFile($report);
	}

}
