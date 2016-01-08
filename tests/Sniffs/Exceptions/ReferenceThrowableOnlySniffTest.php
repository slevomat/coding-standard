<?php

namespace SlevomatCodingStandard\Sniffs\Exceptions;

class ReferenceThrowableOnlySniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testExceptionReferences()
	{
		$report = $this->checkFile(__DIR__ . '/data/exception-references.php');
		$this->assertSniffError($report, 5, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		$this->assertNoSniffError($report, 6);
		$this->assertNoSniffError($report, 8);
		$this->assertSniffError($report, 12, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		$this->assertNoSniffError($report, 13);
		$this->assertNoSniffError($report, 14);
		$this->assertNoSniffError($report, 17);
		$this->assertNoSniffError($report, 18);
		$this->assertSniffError($report, 23, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		$this->assertNoSniffError($report, 25);
		$this->assertNoSniffError($report, 27);
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
		$this->assertSniffError($report, 20, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		$this->assertSniffError($report, 22, ReferenceThrowableOnlySniff::CODE_REFERENCED_GENERAL_EXCEPTION);
		$this->assertNoSniffError($report, 24);
	}

}
