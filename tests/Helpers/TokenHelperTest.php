<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class TokenHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function testFindNextEffective()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/emptyPhpFile.php'
		);
		$this->assertTokenPointer(T_OPEN_TAG, 1, $codeSnifferFile, TokenHelper::findNextEffective($codeSnifferFile, 0));
	}

	public function testFindNextEffectiveAtEndOfFile()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/emptyPhpFile.php'
		);
		$openTagPointer = $codeSnifferFile->findNext(T_OPEN_TAG, 0);
		$this->assertTokenPointer(T_OPEN_TAG, 1, $codeSnifferFile, $openTagPointer);
		$this->assertNull(TokenHelper::findNextEffective($codeSnifferFile, $openTagPointer + 1));
	}

	public function testFindNextEffectiveWithComment()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/effectiveCodeWithComment.php'
		);
		$this->assertTokenPointer(T_CLASS, 5, $codeSnifferFile, TokenHelper::findNextEffective($codeSnifferFile, 1));
	}

	public function testFindNextEffectiveWithDocComment()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/effectiveCodeWithDocComment.php'
		);
		$this->assertTokenPointer(T_CLASS, 8, $codeSnifferFile, TokenHelper::findNextEffective($codeSnifferFile, 1));
	}

	public function testFindNothingNextExcluding()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$this->assertNull(TokenHelper::findNextExcluding($codeSnifferFile, [
			T_OPEN_TAG,
			T_WHITESPACE,
			T_VARIABLE,
			T_EQUAL,
			T_STRING,
			T_OPEN_PARENTHESIS,
			T_CLOSE_PARENTHESIS,
			T_SEMICOLON,
		], 0));
	}

	public function testFindNextExcluding()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$this->assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, TokenHelper::findNextExcluding($codeSnifferFile, [
			T_OPEN_TAG,
			T_WHITESPACE,
		], 0));
	}

	public function testFindNextExcludingWithSpecifiedEndPointer()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$variableTokenPointer = $codeSnifferFile->findNext(T_VARIABLE, 0);
		$this->assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, $variableTokenPointer);
		$this->assertNull(TokenHelper::findNextExcluding($codeSnifferFile, [
			T_OPEN_TAG,
			T_WHITESPACE,
		], 0, $variableTokenPointer));
	}

	public function testFindNextAnyToken()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$variableTokenPointer = $codeSnifferFile->findNext(T_VARIABLE, 0);
		$this->assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, $variableTokenPointer);
		$this->assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, TokenHelper::findNextAnyToken($codeSnifferFile, $variableTokenPointer));
	}

	public function testFindPreviousEffective()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$barTokenPointer = $codeSnifferFile->findNext(T_STRING, 1);
		$assignmentTokenPointer = TokenHelper::findPreviousEffective($codeSnifferFile, $barTokenPointer - 1);
		$this->assertTokenPointer(T_EQUAL, 3, $codeSnifferFile, $assignmentTokenPointer);
	}

	public function testFindPreviousEffectiveWithComment()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/effectiveCodeWithComment.php'
		);
		$this->assertTokenPointer(T_OPEN_TAG, 1, $codeSnifferFile, TokenHelper::findPreviousEffective($codeSnifferFile, $codeSnifferFile->findNext(T_CLASS, 0) - 1));
	}

	public function testFindPreviousEffectiveWithDocComment()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/effectiveCodeWithDocComment.php'
		);
		$this->assertTokenPointer(T_OPEN_TAG, 1, $codeSnifferFile, TokenHelper::findPreviousEffective($codeSnifferFile, $codeSnifferFile->findNext(T_CLASS, 0) - 1));
	}

	public function testFindNothingPreviousExcluding()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$this->assertNull(TokenHelper::findPreviousExcluding($codeSnifferFile, [
			T_OPEN_TAG,
			T_WHITESPACE,
			T_VARIABLE,
			T_EQUAL,
			T_STRING,
			T_OPEN_PARENTHESIS,
			T_CLOSE_PARENTHESIS,
			T_SEMICOLON,
		], TokenHelper::getLastTokenPointer($codeSnifferFile)));
	}

	public function testFindPreviousExcluding()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$this->assertTokenPointer(T_OPEN_PARENTHESIS, 3, $codeSnifferFile, TokenHelper::findPreviousExcluding($codeSnifferFile, [
			T_WHITESPACE,
			T_CLOSE_PARENTHESIS,
			T_SEMICOLON,
		], TokenHelper::getLastTokenPointer($codeSnifferFile)));
	}

	public function testFindPreviousExcludingWithSpecifiedEndPointer()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);

		$lastTokenPointer = TokenHelper::getLastTokenPointer($codeSnifferFile);
		$stringTokenPointer = $codeSnifferFile->findNext(T_STRING, 0);
		$this->assertTokenPointer(T_STRING, 3, $codeSnifferFile, $stringTokenPointer);
		$this->assertTokenPointer(T_STRING, 3, $codeSnifferFile, TokenHelper::findPreviousExcluding($codeSnifferFile, [
			T_WHITESPACE,
			T_OPEN_PARENTHESIS,
			T_CLOSE_PARENTHESIS,
			T_SEMICOLON,
		], $lastTokenPointer, $stringTokenPointer));
	}

	public function testFindNothingPreviousExcludingWithSpecifiedEndPointer()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);

		$lastTokenPointer = TokenHelper::getLastTokenPointer($codeSnifferFile);
		$openParenthesisTokenPointer = $codeSnifferFile->findNext(T_OPEN_PARENTHESIS, 0);
		$this->assertTokenPointer(T_OPEN_PARENTHESIS, 3, $codeSnifferFile, $openParenthesisTokenPointer);
		$this->assertNull(TokenHelper::findPreviousExcluding($codeSnifferFile, [
			T_WHITESPACE,
			T_OPEN_PARENTHESIS,
			T_CLOSE_PARENTHESIS,
			T_SEMICOLON,
		], $lastTokenPointer, $openParenthesisTokenPointer));
	}

	public function testFindFirstTokenOnNextLine()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleTwo.php'
		);
		$variableTokenPointer = $codeSnifferFile->findNext(T_VARIABLE, 0);
		$this->assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, $variableTokenPointer);
		$this->assertTokenPointer(T_STRING, 4, $codeSnifferFile, TokenHelper::findFirstTokenOnNextLine($codeSnifferFile, $variableTokenPointer));
	}

	public function testFindFirstTokenOnIndentedNextLine()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/forLoop.php'
		);
		$tokens = $codeSnifferFile->getTokens();
		$forTokenPointer = $codeSnifferFile->findNext(T_FOR, 0);
		$nextLineTokenPointer = TokenHelper::findFirstTokenOnNextLine($codeSnifferFile, $forTokenPointer);
		$this->assertTokenPointer(T_WHITESPACE, 4, $codeSnifferFile, $nextLineTokenPointer);
		$this->assertSame("\t", $tokens[$nextLineTokenPointer]['content']);
		$fooTokenPointer = TokenHelper::findNextAnyToken($codeSnifferFile, $nextLineTokenPointer + 1);
		$this->assertTokenPointer(T_STRING, 4, $codeSnifferFile, $fooTokenPointer);
		$this->assertSame('foo', $tokens[$fooTokenPointer]['content']);
	}

	public function testFindFirstTokenOnNonExistentNextLine()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleTwo.php'
		);
		$this->assertNull(TokenHelper::findFirstTokenOnNextLine($codeSnifferFile, TokenHelper::getLastTokenPointer($codeSnifferFile)));
	}

	public function testFindFirstTokenOnNonExistentNextLineAfterLastTokenInFile()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleTwo.php'
		);
		$this->assertNull(TokenHelper::findFirstTokenOnNextLine($codeSnifferFile, TokenHelper::getLastTokenPointer($codeSnifferFile) + 1));
	}

	public function testGetContent()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleTwo.php'
		);
		$variableTokenPointer = $codeSnifferFile->findNext(T_VARIABLE, 0);
		$this->assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, $variableTokenPointer);
		$openParenthesisTokenPointer = $codeSnifferFile->findNext(T_OPEN_PARENTHESIS, 0);
		$this->assertTokenPointer(T_OPEN_PARENTHESIS, 4, $codeSnifferFile, $openParenthesisTokenPointer);
		$content = TokenHelper::getContent($codeSnifferFile, $variableTokenPointer, $openParenthesisTokenPointer);
		$this->assertSame(sprintf('$i++;%sfoo', $codeSnifferFile->eolChar), $content);
	}

	public function testGetLastTokenPointer()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$semicolonTokenPointer = $codeSnifferFile->findNext(T_SEMICOLON, 0);
		$this->assertTokenPointer(T_SEMICOLON, 3, $codeSnifferFile, $semicolonTokenPointer);
		$lastWhitespaceTokenPointer = $codeSnifferFile->findNext(T_WHITESPACE, $semicolonTokenPointer + 1);
		$this->assertTokenPointer(T_WHITESPACE, 3, $codeSnifferFile, $lastWhitespaceTokenPointer);
		$this->assertSame($lastWhitespaceTokenPointer, TokenHelper::getLastTokenPointer($codeSnifferFile));
	}

	public function testGetLastTokenPointerInEmptyFile()
	{
		try {
			$codeSnifferFile = $this->getCodeSnifferFile(
				__DIR__ . '/data/emptyFile.php'
			);
			TokenHelper::getLastTokenPointer($codeSnifferFile);
			$this->fail();
		} catch (\SlevomatCodingStandard\Helpers\EmptyFileException $e) {
			$this->assertContains('emptyFile.php is empty', $e->getMessage());
			$this->assertContains('emptyFile.php', $e->getFilename());
		}
	}

}
