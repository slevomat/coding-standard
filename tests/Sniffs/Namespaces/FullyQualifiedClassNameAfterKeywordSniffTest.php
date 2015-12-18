<?php

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class FullyQualifiedClassNameAfterKeywordSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testThrowExceptionForUndefinedConstant()
	{
		try {
			$this->checkFile(
				__DIR__ . '/data/fullyQualifiedExtends.php',
				['keywordsToCheck' => ['T_FOO']]
			);
			$this->fail();
		} catch (\SlevomatCodingStandard\Sniffs\Namespaces\UndefinedKeywordTokenException $e) {
			$this->assertContains('T_FOO', $e->getMessage());
			$this->assertSame('T_FOO', $e->getKeyword());
		}
	}

	public function testThrowExceptionWhenNoKeywordsAreConfigured()
	{
		try {
			$this->checkFile(__DIR__ . '/data/fullyQualifiedExtends.php');
			$this->fail();
		} catch (\SlevomatCodingStandard\Sniffs\Namespaces\NoKeywordsException $e) {
			$this->assertSame('Sniff SlevomatCodingStandard\Sniffs\Namespaces\FullyQualifiedClassNameAfterKeywordSniff requires an array of keywords set in property keywordsToCheck', $e->getMessage());
			$this->assertSame('SlevomatCodingStandard\Sniffs\Namespaces\FullyQualifiedClassNameAfterKeywordSniff', $e->getSniffClassName());
			$this->assertSame('keywordsToCheck', $e->getPropertyName());
		}
	}

}
