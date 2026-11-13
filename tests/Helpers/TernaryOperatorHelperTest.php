<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Runner;
use PHPUnit\Framework\Attributes\DataProvider;
use function array_keys;
use function constant;
use function count;
use function sprintf;
use const T_CLOSE_PARENTHESIS;
use const T_INLINE_THEN;
use const T_LNUMBER;
use const T_SEMICOLON;
use const T_VARIABLE;

/**
 * Tests for SlevomatCodingStandard\Helpers\TernaryOperatorHelper.
 */
class TernaryOperatorHelperTest extends TestCase
{

	/**
	 * Provides test data for testIs().
	 *
	 * @return list<array{0: int, 1: int, 2: bool}>
	 */
	public static function dataIs(): array
	{
		return [
			[6, 1, false],
			[11, 1, true],
		];
	}

	/**
	 * Verifies whether a question mark token represents a ternary operator.
	 *
	 * @dataProvider dataIs
	 * @param int $line Line number of the question mark token.
	 * @param int $occurrence 1-based occurrence index of the question mark on that line.
	 * @param bool $expected Expected return value indicating whether the token is a ternary operator.
	 */
	#[DataProvider('dataIs')]
	public function testIs(int $line, int $occurrence, bool $expected): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/ternaryOperatorHelper.php');
		$pointer = $this->findTokenPointerByLineAndContent($phpcsFile, $line, '?', $occurrence);

		self::assertSame($expected, TernaryOperatorHelper::is($phpcsFile, $pointer));
	}

	/**
	 * Provides test data for testIsConditionOfTernaryOperator().
	 *
	 * @return list<array{0: int, 1: string, 2: string, 3: bool}>
	 */
	public static function dataIsConditionOfTernaryOperator(): array
	{
		return [
			[14, '$b', 'T_VARIABLE', true],
			[14, '$x', 'T_VARIABLE', false],
			[14, '=', 'T_EQUAL', false],
			[186, '$b', 'T_VARIABLE', false],
			[224, '$last', 'T_VARIABLE', false],
		];
	}

	/**
	 * Verifies whether a token belongs to the condition expression of a ternary operator.
	 *
	 * @dataProvider dataIsConditionOfTernaryOperator
	 * @param int $line Line number of the target token.
	 * @param string $content String content of the target token.
	 * @param string $tokenCodeName Token constant name.
	 * @param bool $expected Expected return value indicating whether the token is in the condition.
	 */
	#[DataProvider('dataIsConditionOfTernaryOperator')]
	public function testIsConditionOfTernaryOperator(int $line, string $content, string $tokenCodeName, bool $expected): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/ternaryOperatorHelper.php');
		$tokenCode = constant($tokenCodeName);
		$pointer = $this->findPointerByLineAndTypeAndContent($phpcsFile, $line, $tokenCode, $content);

		self::assertSame($expected, TernaryOperatorHelper::isConditionOfTernaryOperator($phpcsFile, $pointer));
	}

	/**
	 * Provides test data for testGetElsePointer().
	 *
	 * @return list<array{0: int, 1: int, 2: int, 3: int}>
	 */
	public static function dataGetElsePointer(): array
	{
		return [
			[17, 1, 17, 1],
			[20, 1, 20, 1],
			[23, 1, 23, 1],
			[26, 1, 26, 1],
			[29, 1, 29, 2],
			[29, 2, 29, 1],
			[32, 1, 34, 1],
			[33, 1, 33, 1],
		];
	}

	/**
	 * Verifies locating the matching colon token pointer for a given ternary question mark.
	 *
	 * @dataProvider dataGetElsePointer
	 * @param int $thenLine Line number of the ternary question mark.
	 * @param int $thenOccurrence 1-based occurrence of the question mark on that line.
	 * @param int $expectedElseLine Expected line number of the matching colon.
	 * @param int $expectedElseOccurrence Expected occurrence of the colon on that line.
	 */
	#[DataProvider('dataGetElsePointer')]
	public function testGetElsePointer(int $thenLine, int $thenOccurrence, int $expectedElseLine, int $expectedElseOccurrence): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/ternaryOperatorHelper.php');
		$thenPointer = $this->findTokenPointerByLineAndContent($phpcsFile, $thenLine, '?', $thenOccurrence);
		$expectedElsePointer = $this->findTokenPointerByLineAndContent($phpcsFile, $expectedElseLine, ':', $expectedElseOccurrence);

		self::assertSame($expectedElsePointer, TernaryOperatorHelper::getElsePointer($phpcsFile, $thenPointer));
	}

	/**
	 * Provides test data for testGetStartPointer().
	 *
	 * @return list<array{0: int, 1: string, 2: string}>
	 */
	public static function dataGetStartPointer(): array
	{
		return [
			[37, 'T_OPEN_PARENTHESIS', '('],
			[40, 'T_VARIABLE', '$b'],
			[43, 'T_OPEN_SHORT_ARRAY', '['],
			[46, 'T_VARIABLE', '$a'],
			[49, 'T_VARIABLE', '$a'],
			[52, 'T_VARIABLE', '$a'],
			[55, 'T_VARIABLE', '$a'],
			[59, 'T_VARIABLE', '$a'],
			[64, 'T_VARIABLE', '$a'],
			[67, 'T_VARIABLE', '$a'],
			[70, 'T_VARIABLE', '$a'],
			[73, 'T_VARIABLE', '$a'],
			[76, 'T_VARIABLE', '$a'],
			[79, 'T_VARIABLE', '$a'],
			[192, 'T_VARIABLE', '$a'],
			[194, 'T_VARIABLE', '$a'],
			[196, 'T_VARIABLE', '$a'],
			[198, 'T_VARIABLE', '$flag'],
			[200, 'T_VARIABLE', '$flag'],
			[202, 'T_VARIABLE', '$flag'],
			[204, 'T_VARIABLE', '$obj'],
			[209, 'T_VARIABLE', '$a'],
			[210, 'T_VARIABLE', '$a'],
			[219, 'T_VARIABLE', '$a'],
			[220, 'T_VARIABLE', '$b'],
			[221, 'T_VARIABLE', '$b'],
		];
	}

	/**
	 * Verifies locating the first token pointer of the condition expression for a ternary operator.
	 *
	 * @dataProvider dataGetStartPointer
	 * @param int $line Line number of the ternary question mark.
	 * @param string $expectedTokenCodeName Expected token constant name at the start.
	 * @param string $expectedContent Expected content string at the start.
	 */
	#[DataProvider('dataGetStartPointer')]
	public function testGetStartPointer(int $line, string $expectedTokenCodeName, string $expectedContent): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/ternaryOperatorHelper.php');
		$thenPointer = $this->findTokenPointerByLineAndContent($phpcsFile, $line, '?');
		$startPointer = TernaryOperatorHelper::getStartPointer($phpcsFile, $thenPointer);
		$tokens = $phpcsFile->getTokens();

		self::assertSame(constant($expectedTokenCodeName), $tokens[$startPointer]['code']);
		self::assertSame($expectedContent, $tokens[$startPointer]['content']);
	}

	/**
	 * Verifies locating the start pointer of ternary expressions that immediately follow open tags.
	 */
	public function testGetStartPointerWithOpenTags(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/ternaryOperatorWithCloseTag.php');
		$tokens = $phpcsFile->getTokens();

		$thenPointerLine1 = $this->findTokenPointerByLineAndContent($phpcsFile, 1, '?');
		$startPointerLine1 = TernaryOperatorHelper::getStartPointer($phpcsFile, $thenPointerLine1);
		self::assertSame(T_VARIABLE, $tokens[$startPointerLine1]['code']);
		self::assertSame('$a', $tokens[$startPointerLine1]['content']);

		$thenPointerLine2 = $this->findTokenPointerByLineAndContent($phpcsFile, 2, '?');
		$startPointerLine2 = TernaryOperatorHelper::getStartPointer($phpcsFile, $thenPointerLine2);
		self::assertSame(T_VARIABLE, $tokens[$startPointerLine2]['code']);
		self::assertSame('$b', $tokens[$startPointerLine2]['content']);
	}

	/**
	 * Provides test data for testGetEndPointer().
	 *
	 * @return list<array{0: int, 1: int, 2: string, 3: string}>
	 */
	public static function dataGetEndPointer(): array
	{
		return [
			[82, 1, 'T_LNUMBER', '2'],
			[85, 1, 'T_CLOSE_PARENTHESIS', ')'],
			[88, 1, 'T_CLOSE_SHORT_ARRAY', ']'],
			[91, 1, 'T_CLOSE_SQUARE_BRACKET', ']'],
			[94, 1, 'T_CLOSE_CURLY_BRACKET', '}'],
			[99, 1, 'T_LNUMBER', '2'],
			[102, 1, 'T_LNUMBER', '2'],
			[105, 1, 'T_LNUMBER', '2'],
			[108, 1, 'T_LNUMBER', '2'],
			[111, 1, 'T_CLOSE_PARENTHESIS', ')'],
			[114, 1, 'T_CLOSE_SHORT_ARRAY', ']'],
			[117, 1, 'T_LNUMBER', '2'],
			[120, 1, 'T_LNUMBER', '2'],
			[124, 1, 'T_LNUMBER', '2'],
			[129, 1, 'T_VARIABLE', '$b'],
			[129, 2, 'T_VARIABLE', '$c'],
			[132, 1, 'T_LNUMBER', '2'],
			[135, 1, 'T_LNUMBER', '2'],
			[138, 1, 'T_LNUMBER', '2'],
			[141, 1, 'T_CLOSE_CURLY_BRACKET', '}'],
			[188, 1, 'T_CLOSE_CURLY_BRACKET', '}'],
			[190, 1, 'T_LNUMBER', '2'],
			[206, 1, 'T_CLOSE_SHORT_ARRAY', ']'],
			[213, 1, 'T_LNUMBER', '2'],
			[215, 1, 'T_LNUMBER', '2'],
			[217, 1, 'T_CLOSE_PARENTHESIS', ')'],
			[222, 2, 'T_LNUMBER', '2'],
			[225, 1, 'T_LNUMBER', '3'],
			[226, 1, 'T_LNUMBER', '3'],
			[227, 1, 'T_CONSTANT_ENCAPSED_STRING', '\'b.php\''],
			[228, 1, 'T_LNUMBER', '4'],
			[229, 1, 'T_LNUMBER', '3'],
		];
	}

	/**
	 * Verifies locating the last token pointer of a ternary expression false branch.
	 *
	 * @dataProvider dataGetEndPointer
	 * @param int $line Line number of the ternary question mark.
	 * @param int $thenOccurrence 1-based occurrence of the question mark on that line.
	 * @param string $expectedTokenCodeName Expected token constant name at the end.
	 * @param string $expectedContent Expected content string at the end.
	 */
	#[DataProvider('dataGetEndPointer')]
	public function testGetEndPointer(int $line, int $thenOccurrence, string $expectedTokenCodeName, string $expectedContent): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/ternaryOperatorHelper.php');
		$thenPointer = $this->findTokenPointerByLineAndContent($phpcsFile, $line, '?', $thenOccurrence);
		$elsePointer = TernaryOperatorHelper::getElsePointer($phpcsFile, $thenPointer);
		$endPointer = TernaryOperatorHelper::getEndPointer($phpcsFile, $thenPointer, $elsePointer);
		$tokens = $phpcsFile->getTokens();

		self::assertSame(constant($expectedTokenCodeName), $tokens[$endPointer]['code']);
		self::assertSame($expectedContent, $tokens[$endPointer]['content']);
	}

	/**
	 * Verifies that getEndPointer() keeps throw-expression ternaries inside the false-arm scan.
	 */
	public function testGetEndPointerWithThrowExpression(): void
	{
		$runner = new Runner();
		$runner->config = new Config(['-s']);
		$runner->init();

		$phpcsFile = new DummyFile(
			'<?php $r = true ? true : false ?? throw $c ? new Exception() : new LogicException();',
			$runner->ruleset,
			$runner->config,
		);
		$phpcsFile->process();

		$thenPointer = $this->findTokenPointerByLineAndContent($phpcsFile, 1, '?', 1);
		$elsePointer = TernaryOperatorHelper::getElsePointer($phpcsFile, $thenPointer);
		$endPointer = TernaryOperatorHelper::getEndPointer($phpcsFile, $thenPointer, $elsePointer);
		$tokens = $phpcsFile->getTokens();

		self::assertSame(T_CLOSE_PARENTHESIS, $tokens[$endPointer]['code']);
		self::assertSame(')', $tokens[$endPointer]['content']);

		$logicalFile = new DummyFile(
			'<?php $r = $a === 1 ? true : false ?? throw new Exception() and $b = 3;',
			$runner->ruleset,
			$runner->config,
		);
		$logicalFile->process();

		$logicalThenPointer = $this->findTokenPointerByLineAndContent($logicalFile, 1, '?', 1);
		$logicalElsePointer = TernaryOperatorHelper::getElsePointer($logicalFile, $logicalThenPointer);
		$logicalEndPointer = TernaryOperatorHelper::getEndPointer($logicalFile, $logicalThenPointer, $logicalElsePointer);
		$logicalTokens = $logicalFile->getTokens();

		self::assertSame(T_LNUMBER, $logicalTokens[$logicalEndPointer]['code']);
		self::assertSame('3', $logicalTokens[$logicalEndPointer]['content']);

		$nestedThrowFile = new DummyFile(
			'<?php $r = true ? true : false ?? $b = true ? throw new Exception() : 3 and $c = 4;',
			$runner->ruleset,
			$runner->config,
		);
		$nestedThrowFile->process();

		$nestedThenPointer = $this->findTokenPointerByLineAndContent($nestedThrowFile, 1, '?', 1);
		$nestedElsePointer = TernaryOperatorHelper::getElsePointer($nestedThrowFile, $nestedThenPointer);
		$nestedEndPointer = TernaryOperatorHelper::getEndPointer($nestedThrowFile, $nestedThenPointer, $nestedElsePointer);
		$nestedTokens = $nestedThrowFile->getTokens();

		self::assertSame(T_LNUMBER, $nestedTokens[$nestedEndPointer]['code']);
		self::assertSame('3', $nestedTokens[$nestedEndPointer]['content']);

		$throwBranchFile = new DummyFile('<?php $r = !$a ? throw new Exception() : $a and $b = 1;', $runner->ruleset, $runner->config);
		$throwBranchFile->process();

		$throwThenPointer = $this->findTokenPointerByLineAndContent($throwBranchFile, 1, '?', 1);
		$throwElsePointer = TernaryOperatorHelper::getElsePointer($throwBranchFile, $throwThenPointer);
		self::assertTrue(
			TernaryOperatorHelper::hasUnparenthesizedLowPrecedenceExpression(
				$throwBranchFile,
				TokenHelper::findNextEffective($throwBranchFile, $throwThenPointer + 1),
				TokenHelper::findPreviousEffective($throwBranchFile, $throwElsePointer - 1),
			),
		);
	}

	/**
	 * Verifies locating the end pointer of a ternary expression terminating at an inline PHP close tag.
	 */
	public function testGetEndPointerWithCloseTag(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/ternaryOperatorWithCloseTag.php');
		$tokens = $phpcsFile->getTokens();

		$thenPointer = $this->findTokenPointerByLineAndContent($phpcsFile, 2, '?');
		$elsePointer = TernaryOperatorHelper::getElsePointer($phpcsFile, $thenPointer);
		$endPointer = TernaryOperatorHelper::getEndPointer($phpcsFile, $thenPointer, $elsePointer);

		self::assertSame(T_LNUMBER, $tokens[$endPointer]['code']);
		self::assertSame('4', $tokens[$endPointer]['content']);
	}

	/**
	 * Verifies locating the end pointer of a ternary expression at the end of a file without a terminating semicolon.
	 */
	public function testGetEndPointerAtEndOfFileWithoutDelimiter(): void
	{
		$codeSniffer = new Runner();
		$codeSniffer->config = new Config(['-s']);
		$codeSniffer->init();
		$codeSniffer->config->tabWidth = 4;
		$phpcsFile = new DummyFile('<?php $a ? 1 : 2', $codeSniffer->ruleset, $codeSniffer->config);
		$phpcsFile->process();

		$thenPointer = TokenHelper::findNext($phpcsFile, T_INLINE_THEN, 0);
		self::assertNotNull($thenPointer);

		$elsePointer = TernaryOperatorHelper::getElsePointer($phpcsFile, $thenPointer);
		$endPointer = TernaryOperatorHelper::getEndPointer($phpcsFile, $thenPointer, $elsePointer);
		$tokens = $phpcsFile->getTokens();

		self::assertSame(T_LNUMBER, $tokens[$endPointer]['code']);
		self::assertSame('2', $tokens[$endPointer]['content']);
	}

	/**
	 * Provides test data for testIgnoreConflictingIndentationSniffs().
	 *
	 * @return list<array{0: int, 1: list<int>}>
	 */
	public static function dataIgnoreConflictingIndentationSniffs(): array
	{
		return [
			[147, []],
			[150, [151, 152]],
			[156, [156, 157]],
			[161, [161, 162]],
			[165, [166]],
		];
	}

	/**
	 * Verifies configuring line ignore entries to avoid conflict with external indentation sniffs.
	 *
	 * @dataProvider dataIgnoreConflictingIndentationSniffs
	 * @param int $line Line number of the ternary question mark.
	 * @param list<int> $expectedIgnoredLines List of 1-based line numbers expected to be marked ignored.
	 */
	#[DataProvider('dataIgnoreConflictingIndentationSniffs')]
	public function testIgnoreConflictingIndentationSniffs(int $line, array $expectedIgnoredLines): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/ternaryOperatorHelper.php');
		$thenPointer = $this->findTokenPointerByLineAndContent($phpcsFile, $line, '?');

		TernaryOperatorHelper::ignoreConflictingIndentationSniffs($phpcsFile, $thenPointer);

		self::assertSame($expectedIgnoredLines, array_keys($phpcsFile->tokenizer->ignoredLines));
	}

	/**
	 * Provides test data for testHasUnparenthesizedTernary().
	 *
	 * @return list<array{0: string, 1: bool}>
	 */
	public static function dataHasUnparenthesizedTernary(): array
	{
		return [
			['$h1', false],
			['$h2', false],
			['$h3', false],
			['$h4', false],
			['$h5', true],
			['$h6', false],
		];
	}

	/**
	 * Verifies determining whether an expression contains an unparenthesized ternary operator at top level.
	 *
	 * @dataProvider dataHasUnparenthesizedTernary
	 * @param string $variableName Variable name in the test fixture file identifying the assignment expression.
	 * @param bool $expected Expected return value indicating presence of an unparenthesized ternary.
	 */
	#[DataProvider('dataHasUnparenthesizedTernary')]
	public function testHasUnparenthesizedTernary(string $variableName, bool $expected): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/ternaryOperatorHelper.php');

		$variablePointer = TokenHelper::findNextContent($phpcsFile, T_VARIABLE, $variableName, 0);
		self::assertNotNull($variablePointer);

		$equalPointer = TokenHelper::findNext($phpcsFile, constant('T_EQUAL'), $variablePointer);
		self::assertNotNull($equalPointer);

		$startPointer = TokenHelper::findNextEffective($phpcsFile, $equalPointer + 1);
		self::assertNotNull($startPointer);

		$semicolonPointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $startPointer);
		self::assertNotNull($semicolonPointer);

		$endPointer = TokenHelper::findPreviousEffective($phpcsFile, $semicolonPointer - 1);
		self::assertNotNull($endPointer);

		self::assertSame(
			$expected,
			TernaryOperatorHelper::hasUnparenthesizedTernary($phpcsFile, $startPointer, $endPointer),
		);
	}

	/**
	 * Finds a token pointer on a specific line matching the given string content and occurrence.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $line Line number to search on.
	 * @param string $content String content of the token to find.
	 * @param int $occurrence 1-based occurrence index on that line (defaults to 1).
	 * @return int The token pointer found.
	 */
	private function findTokenPointerByLineAndContent(File $phpcsFile, int $line, string $content, int $occurrence = 1): int
	{
		$tokens = $phpcsFile->getTokens();
		$foundOccurrence = 0;
		for ($i = 0; $i < count($tokens); $i++) {
			if ($tokens[$i]['line'] > $line) {
				break;
			}

			if ($tokens[$i]['line'] < $line) {
				continue;
			}

			if ($tokens[$i]['content'] !== $content) {
				continue;
			}

			$foundOccurrence++;
			if ($foundOccurrence === $occurrence) {
				return $i;
			}
		}

		self::fail(sprintf('Token with content "%s" on line %d (occurrence %d) not found.', $content, $line, $occurrence));
	}

	/**
	 * Finds a token pointer on a specific line matching the given token code and content.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $line Line number to search on.
	 * @param int|string $tokenCode Token constant or code to match.
	 * @param string $content Token content to match.
	 * @return int The token pointer found.
	 */
	private function findPointerByLineAndTypeAndContent(File $phpcsFile, int $line, $tokenCode, string $content): int
	{
		$tokens = $phpcsFile->getTokens();
		for ($i = 0; $i < count($tokens); $i++) {
			if ($tokens[$i]['line'] > $line) {
				break;
			}

			if ($tokens[$i]['line'] < $line) {
				continue;
			}

			if ($tokens[$i]['code'] !== $tokenCode || $tokens[$i]['content'] !== $content) {
				continue;
			}

			return $i;
		}

		self::fail(sprintf('Token with code %s and content "%s" on line %d not found.', (string) $tokenCode, $content, $line));
	}

}
