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

		self::assertSame(66, $report->getErrorCount());

		self::assertSniffError(
			$report,
			16,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\PropertySameNamespace in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			19,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\PropertyUsed in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			22,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\Partial\PropertyPartiallyUsed in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			29,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\Foo in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			34,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\Foo in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			38,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\Bar in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			38,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\Foo in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			41,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\Foo in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			53,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\Foo in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			58,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\VariableSameNamespace in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			64,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\VariableWithCommentSameNamespace in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			72,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\ParamSameNamespace in @param should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			74,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\ReturnSameNamespace in @return should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			82,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\Partial in @param should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			92,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @property should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			93,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @property-read should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			94,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @property-write should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			95,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			95,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Traversable in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			95,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Exception in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			96,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			96,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			97,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			97,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			97,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTime in @method should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			105,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @property should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			105,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @property should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			110,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			110,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			113,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			113,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Traversable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			113,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			116,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			116,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTime in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			119,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			119,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTime in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			122,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			125,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			125,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTime in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			128,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			131,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			131,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Traversable in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			131,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @var should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			140,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @return should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			140,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Traversable in @return should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			149,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @var should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			152,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @var should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			156,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			157,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @method should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			157,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Traversable in @method should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			165,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTime in @template should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			166,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @template-covariant should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			167,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @template-extends should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			167,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @template-extends should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			168,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @template-implements should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			168,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @template-implements should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			169,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \Iterator in @template-use should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			169,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @template-use should be referenced via a fully qualified name'
		);
		self::assertSniffError(
			$report,
			175,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \XXX\TemplateThatDoesNotExist in @phpstan-return should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			185,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \YYY\PropertyUsed in @mixin should be referenced via a fully qualified name'
		);

		self::assertSniffError(
			$report,
			196,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTimeImmutable in @param should be referenced via a fully qualified name'
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
			210,
			FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME,
			'Class name \DateTime in @param should be referenced via a fully qualified name'
		);

		self::assertAllFixedInFile($report);
	}

}
