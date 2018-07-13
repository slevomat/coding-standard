<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use const T_CLASS;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSURE;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_EQUAL;
use const T_FOR;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_TAG;
use const T_SEMICOLON;
use const T_STRING;
use const T_VARIABLE;
use const T_WHITESPACE;
use function sprintf;

class TokenHelperTest extends TestCase
{

	public function testFindNextAll(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/useStatements.php'
		);

		$tokens = $codeSnifferFile->getTokens();

		$pointers = TokenHelper::findNextAll($codeSnifferFile, [T_OPEN_TAG, T_CLASS, T_CLOSURE], 0);

		self::assertCount(4, $pointers);

		self::assertSame(T_OPEN_TAG, $tokens[$pointers[0]]['code']);
		self::assertSame(T_CLASS, $tokens[$pointers[1]]['code']);
		self::assertSame(T_CLOSURE, $tokens[$pointers[2]]['code']);
		self::assertSame(T_CLOSURE, $tokens[$pointers[3]]['code']);
	}

	public function testFindNextEffective(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/emptyPhpFile.php'
		);
		self::assertTokenPointer(T_OPEN_TAG, 1, $codeSnifferFile, TokenHelper::findNextEffective($codeSnifferFile, 0));
	}

	public function testFindNextEffectiveAtEndOfFile(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/emptyPhpFile.php'
		);
		$openTagPointer = TokenHelper::findNext($codeSnifferFile, T_OPEN_TAG, 0);
		self::assertTokenPointer(T_OPEN_TAG, 1, $codeSnifferFile, $openTagPointer);
		self::assertNull(TokenHelper::findNextEffective($codeSnifferFile, $openTagPointer + 1));
	}

	public function testFindNextEffectiveWithComment(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/effectiveCodeWithComment.php'
		);
		self::assertTokenPointer(T_CLASS, 5, $codeSnifferFile, TokenHelper::findNextEffective($codeSnifferFile, 1));
	}

	public function testFindNextEffectiveWithDocComment(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/effectiveCodeWithDocComment.php'
		);
		self::assertTokenPointer(T_CLASS, 8, $codeSnifferFile, TokenHelper::findNextEffective($codeSnifferFile, 1));
	}

	public function testFindNothingNextExcluding(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		self::assertNull(TokenHelper::findNextExcluding($codeSnifferFile, [
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
		self::assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, TokenHelper::findNextExcluding($codeSnifferFile, [
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
		self::assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, $variableTokenPointer);
		self::assertNull(TokenHelper::findNextExcluding($codeSnifferFile, [
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
		self::assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, $variableTokenPointer);
		self::assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, TokenHelper::findNextAnyToken($codeSnifferFile, $variableTokenPointer));
	}

	public function testFindPreviousEffective(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$barTokenPointer = TokenHelper::findNext($codeSnifferFile, T_STRING, 1);
		$assignmentTokenPointer = TokenHelper::findPreviousEffective($codeSnifferFile, $barTokenPointer - 1);
		self::assertTokenPointer(T_EQUAL, 3, $codeSnifferFile, $assignmentTokenPointer);
	}

	public function testFindPreviousEffectiveWithComment(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/effectiveCodeWithComment.php'
		);
		self::assertTokenPointer(T_OPEN_TAG, 1, $codeSnifferFile, TokenHelper::findPreviousEffective($codeSnifferFile, TokenHelper::findNext($codeSnifferFile, T_CLASS, 0) - 1));
	}

	public function testFindPreviousEffectiveWithDocComment(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/effectiveCodeWithDocComment.php'
		);
		self::assertTokenPointer(T_OPEN_TAG, 1, $codeSnifferFile, TokenHelper::findPreviousEffective($codeSnifferFile, TokenHelper::findNext($codeSnifferFile, T_CLASS, 0) - 1));
	}

	public function testFindNothingPreviousExcluding(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		self::assertNull(TokenHelper::findPreviousExcluding($codeSnifferFile, [
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
		self::assertTokenPointer(T_OPEN_PARENTHESIS, 3, $codeSnifferFile, TokenHelper::findPreviousExcluding($codeSnifferFile, [
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
		self::assertTokenPointer(T_STRING, 3, $codeSnifferFile, $stringTokenPointer);
		self::assertTokenPointer(T_STRING, 3, $codeSnifferFile, TokenHelper::findPreviousExcluding($codeSnifferFile, [
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
		self::assertTokenPointer(T_OPEN_PARENTHESIS, 3, $codeSnifferFile, $openParenthesisTokenPointer);
		self::assertNull(TokenHelper::findPreviousExcluding($codeSnifferFile, [
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
		self::assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, $variableTokenPointer);
		self::assertTokenPointer(T_STRING, 4, $codeSnifferFile, TokenHelper::findFirstTokenOnNextLine($codeSnifferFile, $variableTokenPointer));
	}

	public function testFindFirstTokenOnNextLineInDocComment(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleThree.php'
		);
		$docBlockTokenPointer = TokenHelper::findNext($codeSnifferFile, T_DOC_COMMENT_OPEN_TAG, 0);
		self::assertTokenPointer(T_DOC_COMMENT_OPEN_TAG, 3, $codeSnifferFile, $docBlockTokenPointer);
		self::assertTokenPointer(T_DOC_COMMENT_WHITESPACE, 4, $codeSnifferFile, TokenHelper::findFirstTokenOnNextLine($codeSnifferFile, $docBlockTokenPointer));
	}

	public function testFindFirstTokenOnIndentedNextLine(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/forLoop.php'
		);
		$tokens = $codeSnifferFile->getTokens();
		$forTokenPointer = TokenHelper::findNext($codeSnifferFile, T_FOR, 0);
		$nextLineTokenPointer = TokenHelper::findFirstTokenOnNextLine($codeSnifferFile, $forTokenPointer);
		self::assertTokenPointer(T_WHITESPACE, 4, $codeSnifferFile, $nextLineTokenPointer);
		self::assertSame("\t", $tokens[$nextLineTokenPointer]['content']);
		$fooTokenPointer = TokenHelper::findNextAnyToken($codeSnifferFile, $nextLineTokenPointer + 1);
		self::assertTokenPointer(T_STRING, 4, $codeSnifferFile, $fooTokenPointer);
		self::assertSame('foo', $tokens[$fooTokenPointer]['content']);
	}

	public function testFindFirstTokenOnNonExistentNextLine(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleTwo.php'
		);
		self::assertNull(TokenHelper::findFirstTokenOnNextLine($codeSnifferFile, TokenHelper::getLastTokenPointer($codeSnifferFile)));
	}

	public function testFindFirstTokenOnNonExistentNextLineAfterLastTokenInFile(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleTwo.php'
		);
		self::assertNull(TokenHelper::findFirstTokenOnNextLine($codeSnifferFile, TokenHelper::getLastTokenPointer($codeSnifferFile) + 1));
	}

	public function testGetContent(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleTwo.php'
		);
		$variableTokenPointer = TokenHelper::findNext($codeSnifferFile, T_VARIABLE, 0);
		self::assertTokenPointer(T_VARIABLE, 3, $codeSnifferFile, $variableTokenPointer);
		$openParenthesisTokenPointer = TokenHelper::findNext($codeSnifferFile, T_OPEN_PARENTHESIS, 0);
		self::assertTokenPointer(T_OPEN_PARENTHESIS, 4, $codeSnifferFile, $openParenthesisTokenPointer);
		$content = TokenHelper::getContent($codeSnifferFile, $variableTokenPointer, $openParenthesisTokenPointer);
		self::assertSame(sprintf('$i++;%sfoo(', $codeSnifferFile->eolChar), $content);
	}

	public function testGetLastTokenPointer(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/sampleOne.php'
		);
		$semicolonTokenPointer = TokenHelper::findNext($codeSnifferFile, T_SEMICOLON, 0);
		self::assertTokenPointer(T_SEMICOLON, 3, $codeSnifferFile, $semicolonTokenPointer);
		$lastWhitespaceTokenPointer = TokenHelper::findNext($codeSnifferFile, T_WHITESPACE, $semicolonTokenPointer + 1);
		self::assertTokenPointer(T_WHITESPACE, 3, $codeSnifferFile, $lastWhitespaceTokenPointer);
		self::assertSame($lastWhitespaceTokenPointer, TokenHelper::getLastTokenPointer($codeSnifferFile));
	}

	public function testGetLastTokenPointerInEmptyFile(): void
	{
		try {
			$codeSnifferFile = $this->getCodeSnifferFile(
				__DIR__ . '/data/emptyFile.php'
			);
			TokenHelper::getLastTokenPointer($codeSnifferFile);
			$this->fail();
		} catch (EmptyFileException $e) {
			self::assertContains('emptyFile.php is empty', $e->getMessage());
			self::assertContains('emptyFile.php', $e->getFilename());
		}
	}

}
