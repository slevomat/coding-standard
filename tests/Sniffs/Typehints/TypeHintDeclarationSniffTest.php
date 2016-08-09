<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Typehints;

class TypeHintDeclarationSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/typeHintDeclarationNoErrors.php', [
			'traversableTypeHints' => [
				\Traversable::class,
				'\QueryResultSet',
				'\FooNamespace\ClassFromCurrentNamespace',
				'\UsedNamespace\UsedClass',
			],
			'usefulAnnotations' => [
				'@see',
			],
			'externalNamespaces' => [
				'\External'
			],
		]));
	}

	public function testErrors()
	{
		$report = $this->checkFile(__DIR__ . '/data/typeHintDeclarationErrors.php', [
			'traversableTypeHints' => [
				\Traversable::class,
			],
		]);

		$this->assertSame(14, $report->getErrorCount());

		$this->assertSniffError($report, 6, TypeHintDeclarationSniff::MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 13, TypeHintDeclarationSniff::MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 20, TypeHintDeclarationSniff::MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 27, TypeHintDeclarationSniff::MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 34, TypeHintDeclarationSniff::MISSING_PARAMETER_TYPE_HINT);
		$this->assertSniffError($report, 41, TypeHintDeclarationSniff::MISSING_PARAMETER_TYPE_HINT);

		$this->assertSniffError($report, 45, TypeHintDeclarationSniff::MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 53, TypeHintDeclarationSniff::MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 61, TypeHintDeclarationSniff::MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 69, TypeHintDeclarationSniff::MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 77, TypeHintDeclarationSniff::MISSING_RETURN_TYPE_HINT);
		$this->assertSniffError($report, 85, TypeHintDeclarationSniff::MISSING_RETURN_TYPE_HINT);

		$this->assertSniffError($report, 93, TypeHintDeclarationSniff::USELESS_DOC_COMMENT);
		$this->assertSniffError($report, 100, TypeHintDeclarationSniff::USELESS_DOC_COMMENT);
	}

}
