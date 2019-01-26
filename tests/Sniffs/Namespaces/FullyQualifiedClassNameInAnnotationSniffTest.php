<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class FullyQualifiedClassNameInAnnotationSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/fullyQualifiedClassNameInAnnotationNoErrors.php'));
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fullyQualifiedClassNameInAnnotationErrors.php');

		self::assertSame(21, $report->getErrorCount());

		self::assertSniffError($report, 13, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		self::assertSniffError($report, 17, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		self::assertSniffError($report, 22, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		self::assertSniffError($report, 26, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		self::assertSniffError($report, 31, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		self::assertSniffError($report, 32, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		self::assertSniffError($report, 40, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		self::assertSniffError($report, 41, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		self::assertSniffError($report, 49, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		self::assertSniffError($report, 57, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME, 'Class name \FooNamespace\FooClass in @return should be referenced via a fully qualified name');
		self::assertSniffError($report, 57, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME, 'Class name \Iterator in @return should be referenced via a fully qualified name');

		self::assertSniffError($report, 68, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME, 'Class name \DateTimeImmutable in @property should be referenced via a fully qualified name');
		self::assertSniffError($report, 69, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME, 'Class name \Iterator in @property-read should be referenced via a fully qualified name');
		self::assertSniffError($report, 70, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME, 'Class name \DateTimeImmutable in @property-write should be referenced via a fully qualified name');
		self::assertSniffError($report, 71, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME, 'Class name \Iterator in @method should be referenced via a fully qualified name');
		self::assertSniffError($report, 71, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME, 'Class name \Traversable in @method should be referenced via a fully qualified name');
		self::assertSniffError($report, 71, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME, 'Class name \Exception in @method should be referenced via a fully qualified name');
		self::assertSniffError($report, 72, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME, 'Class name \DateTimeImmutable in @method should be referenced via a fully qualified name');
		self::assertSniffError($report, 72, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME, 'Class name \DateTimeImmutable in @method should be referenced via a fully qualified name');
		self::assertSniffError($report, 73, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME, 'Class name \DateTimeImmutable in @method should be referenced via a fully qualified name');
		self::assertSniffError($report, 73, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME, 'Class name \Iterator in @method should be referenced via a fully qualified name');
	}

	public function testFixableFullyQualified(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableFullyQualifiedClassNameInAnnotation.php');
		self::assertAllFixedInFile($report);
	}

}
