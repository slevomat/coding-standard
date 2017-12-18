<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class FullyQualifiedClassNameInAnnotationSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/fullyQualifiedClassNameInAnnotationNoErrors.php'));
	}

	public function testErrors()
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedClassNameInAnnotationErrors.php');

		$this->assertSame(12, $report->getErrorCount());

		$this->assertSniffError($report, 13, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		$this->assertSniffError($report, 17, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		$this->assertSniffError($report, 22, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		$this->assertSniffError($report, 26, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		$this->assertSniffError($report, 31, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		$this->assertSniffError($report, 32, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		$this->assertSniffError($report, 40, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		$this->assertSniffError($report, 41, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		$this->assertSniffError($report, 49, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
		$this->assertSniffError($report, 57, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME, 'Class name \FooNamespace\FooClass in @return should be referenced via a fully qualified name');
		$this->assertSniffError($report, 57, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME, 'Class name \Iterator in @return should be referenced via a fully qualified name');
		$this->assertSniffError($report, 61, FullyQualifiedClassNameInAnnotationSniff::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
	}

	public function testFixableFullyQualified()
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableFullyQualifiedClassNameInAnnotation.php');
		$this->assertAllFixedInFile($report);
	}

}
