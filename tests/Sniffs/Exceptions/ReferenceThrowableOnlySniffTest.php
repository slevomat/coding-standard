<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Exceptions;

class ReferenceThrowableOnlySniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testExceptionReferences()
	{
		$report = $this->checkFile(__DIR__ . '/data/exception-references.php');
		$this->assertNoSniffError($report, 5);
		$this->assertNoSniffError($report, 6);
		$this->assertNoSniffError($report, 8);
		$this->assertSniffError($report, 12, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		$this->assertNoSniffError($report, 13);
		$this->assertNoSniffError($report, 14);
		$this->assertNoSniffError($report, 17);
		$this->assertNoSniffError($report, 18);
		$this->assertNoSniffError($report, 23);
		$this->assertNoSniffError($report, 25);
		$this->assertNoSniffError($report, 27);
		$this->assertSniffError($report, 33, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		$this->assertNoSniffError($report, 35);
		$this->assertSniffError($report, 37, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
	}

	public function testExceptionReferencesWithoutNamespace()
	{
		$report = $this->checkFile(__DIR__ . '/data/exception-references-without-namespace.php');
		$this->assertNoSniffError($report, 3);
		$this->assertNoSniffError($report, 5);
		$this->assertSniffError($report, 9, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		$this->assertSniffError($report, 10, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		$this->assertNoSniffError($report, 11);
		$this->assertNoSniffError($report, 14);
		$this->assertNoSniffError($report, 15);
		$this->assertNoSniffError($report, 20);
		$this->assertNoSniffError($report, 22);
		$this->assertNoSniffError($report, 24);
		$this->assertSniffError($report, 30, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		$this->assertSniffError($report, 32, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Typehints.TypeHintDeclaration.uselessDocComment
	 * @requires PHP 7.1
	 */
	public function testExceptionReferencesUnionTypes71()
	{
		$report = $this->checkFile(__DIR__ . '/data/exception-references-71.php');
		$this->assertNoSniffError($report, 5);
		$this->assertNoSniffError($report, 7);
		$this->assertNoSniffError($report, 9);
		$this->assertNoSniffError($report, 15);
		$this->assertNoSniffError($report, 17);
		$this->assertNoSniffError($report, 19);
		$this->assertSniffError($report, 25, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		$this->assertSniffError($report, 27, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
	}

}
