<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class TokenHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function testFindNextEffective(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/emptyPhpFile.php'
		);
		$this->assertTokenPointer(T_OPEN_TAG, 1, $codeSnifferFile, TokenHelper::findNextEffective($codeSnifferFile, 0));
	}

	public function testFindNextEffectiveAtEndOfFile(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/emptyPhpFile.php'
		);
		$openTagPointer = TokenHelper::findNext($codeSnifferFile, T_OPEN_TAG, 0);
		$this->assertTokenPointer(T_OPEN_TAG, 1, $codeSnifferFile, $openTagPointer);
		$this->assertNull(TokenHelper::findNextEffective($codeSnifferFile, $openTagPointer + 1));
	}

	public function testFindNextEffectiveWithComment(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/effectiveCodeWithComment.php'
		);
		$this->assertTokenPointer(T_CLASS, 5, $codeSnifferFile, TokenHelper::findNextEffective($codeSnifferFile, 1));
	}

	public function testFindNextEffectiveWithDocComment(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/effectiveCodeWithDocComment.php'
		);
		$this->assertTokenPointer(T_CLASS, 8, $codeSnifferFile, TokenHelper::findNextEffective($codeSnifferFile, 1));
	}

	public function testFindNothingNextExcluding(): void
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

	public function testFindNextExcluding(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$this->assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, TokenHelper::findNextExcluding($codeSnifferFile, [
			T_OPEN_TAG,
			T_WHITESPACE,
		], 0));
	}

	public function testFindNextExcludingWithSpecifiedEndPointer(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$variableTokenPointer = TokenHelper::findNext($codeSnifferFile, T_VARIABLE, 0);
		$this->assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, $variableTokenPointer);
		$this->assertNull(TokenHelper::findNextExcluding($codeSnifferFile, [
			T_OPEN_TAG,
			T_WHITESPACE,
		], 0, $variableTokenPointer));
	}

	public function testFindNextAnyToken(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$variableTokenPointer = TokenHelper::findNext($codeSnifferFile, T_VARIABLE, 0);
		$this->assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, $variableTokenPointer);
		$this->assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, TokenHelper::findNextAnyToken($codeSnifferFile, $variableTokenPointer));
	}

	public function testFindPreviousEffective(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$barTokenPointer = TokenHelper::findNext($codeSnifferFile, T_STRING, 1);
		$assignmentTokenPointer = TokenHelper::findPreviousEffective($codeSnifferFile, $barTokenPointer - 1);
		$this->assertTokenPointer(T_EQUAL, 3, $codeSnifferFile, $assignmentTokenPointer);
	}

	public function testFindPreviousEffectiveWithComment(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/effectiveCodeWithComment.php'
		);
		$this->assertTokenPointer(T_OPEN_TAG, 1, $codeSnifferFile, TokenHelper::findPreviousEffective($codeSnifferFile, TokenHelper::findNext($codeSnifferFile, T_CLASS, 0) - 1));
	}

	public function testFindPreviousEffectiveWithDocComment(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/effectiveCodeWithDocComment.php'
		);
		$this->assertTokenPointer(T_OPEN_TAG, 1, $codeSnifferFile, TokenHelper::findPreviousEffective($codeSnifferFile, TokenHelper::findNext($codeSnifferFile, T_CLASS, 0) - 1));
	}

	public function testFindNothingPreviousExcluding(): void
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

	public function testFindPreviousExcluding(): void
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

	public function testFindPreviousExcludingWithSpecifiedEndPointer(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);

		$lastTokenPointer = TokenHelper::getLastTokenPointer($codeSnifferFile);
		$stringTokenPointer = TokenHelper::findNext($codeSnifferFile, T_STRING, 0);
		$this->assertTokenPointer(T_STRING, 3, $codeSnifferFile, $stringTokenPointer);
		$this->assertTokenPointer(T_STRING, 3, $codeSnifferFile, TokenHelper::findPreviousExcluding($codeSnifferFile, [
			T_WHITESPACE,
			T_OPEN_PARENTHESIS,
			T_CLOSE_PARENTHESIS,
			T_SEMICOLON,
		], $lastTokenPointer, $stringTokenPointer));
	}

	public function testFindNothingPreviousExcludingWithSpecifiedEndPointer(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);

		$lastTokenPointer = TokenHelper::getLastTokenPointer($codeSnifferFile);
		$openParenthesisTokenPointer = TokenHelper::findNext($codeSnifferFile, T_OPEN_PARENTHESIS, 0);
		$this->assertTokenPointer(T_OPEN_PARENTHESIS, 3, $codeSnifferFile, $openParenthesisTokenPointer);
		$this->assertNull(TokenHelper::findPreviousExcluding($codeSnifferFile, [
			T_WHITESPACE,
			T_OPEN_PARENTHESIS,
			T_CLOSE_PARENTHESIS,
			T_SEMICOLON,
		], $lastTokenPointer, $openParenthesisTokenPointer));
	}

	public function testFindFirstTokenOnNextLine(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleTwo.php'
		);
		$variableTokenPointer = TokenHelper::findNext($codeSnifferFile, T_VARIABLE, 0);
		$this->assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, $variableTokenPointer);
		$this->assertTokenPointer(T_STRING, 4, $codeSnifferFile, TokenHelper::findFirstTokenOnNextLine($codeSnifferFile, $variableTokenPointer));
	}

	public function testFindFirstTokenOnIndentedNextLine(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/forLoop.php'
		);
		$tokens = $codeSnifferFile->getTokens();
		$forTokenPointer = TokenHelper::findNext($codeSnifferFile, T_FOR, 0);
		$nextLineTokenPointer = TokenHelper::findFirstTokenOnNextLine($codeSnifferFile, $forTokenPointer);
		$this->assertTokenPointer(T_WHITESPACE, 4, $codeSnifferFile, $nextLineTokenPointer);
		$this->assertSame("\t", $tokens[$nextLineTokenPointer]['content']);
		$fooTokenPointer = TokenHelper::findNextAnyToken($codeSnifferFile, $nextLineTokenPointer + 1);
		$this->assertTokenPointer(T_STRING, 4, $codeSnifferFile, $fooTokenPointer);
		$this->assertSame('foo', $tokens[$fooTokenPointer]['content']);
	}

	public function testFindFirstTokenOnNonExistentNextLine(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleTwo.php'
		);
		$this->assertNull(TokenHelper::findFirstTokenOnNextLine($codeSnifferFile, TokenHelper::getLastTokenPointer($codeSnifferFile)));
	}

	public function testFindFirstTokenOnNonExistentNextLineAfterLastTokenInFile(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleTwo.php'
		);
		$this->assertNull(TokenHelper::findFirstTokenOnNextLine($codeSnifferFile, TokenHelper::getLastTokenPointer($codeSnifferFile) + 1));
	}

	public function testGetContent(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleTwo.php'
		);
		$variableTokenPointer = TokenHelper::findNext($codeSnifferFile, T_VARIABLE, 0);
		$this->assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, $variableTokenPointer);
		$openParenthesisTokenPointer = TokenHelper::findNext($codeSnifferFile, T_OPEN_PARENTHESIS, 0);
		$this->assertTokenPointer(T_OPEN_PARENTHESIS, 4, $codeSnifferFile, $openParenthesisTokenPointer);
		$content = TokenHelper::getContent($codeSnifferFile, $variableTokenPointer, $openParenthesisTokenPointer);
		$this->assertSame(sprintf('$i++;%sfoo(', $codeSnifferFile->eolChar), $content);
	}

	public function testGetLastTokenPointer(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$semicolonTokenPointer = TokenHelper::findNext($codeSnifferFile, T_SEMICOLON, 0);
		$this->assertTokenPointer(T_SEMICOLON, 3, $codeSnifferFile, $semicolonTokenPointer);
		$lastWhitespaceTokenPointer = TokenHelper::findNext($codeSnifferFile, T_WHITESPACE, $semicolonTokenPointer + 1);
		$this->assertTokenPointer(T_WHITESPACE, 3, $codeSnifferFile, $lastWhitespaceTokenPointer);
		$this->assertSame($lastWhitespaceTokenPointer, TokenHelper::getLastTokenPointer($codeSnifferFile));
	}

	public function testGetLastTokenPointerInEmptyFile(): void
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
