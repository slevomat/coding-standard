<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use Generator;
use PHP_CodeSniffer\Files\File;
use function array_key_exists;
use function basename;
use function in_array;
use function sprintf;
use function strpos;
use function trim;

class ArrayHelperTest extends TestCase
{

	/**
	 * @dataProvider parseProvider
	 * @param array<string, mixed> $expect flags and various expected values for the given array
	 */
	public function testParse(File $phpcsFile, int $pointer, array $expect): void
	{
		$parsed = ArrayHelper::parse($phpcsFile, $pointer);
		$tokens = $phpcsFile->getTokens();

		foreach ($parsed as $keyValue) {
			/** @phpstan-var mixed $keyValue phpstan making assumption based on phpdoc */
			self::assertInstanceOf(ArrayKeyValue::class, $keyValue);
		}

		if (isset($expect['count'])) {
			self::assertCount((int) $expect['count'], $parsed);
		}
		foreach ((array) $expect['keyValues'] as $i => $keyValueInfoExpect) {
			$keyValueInfoExpect = (array) $keyValueInfoExpect;
			$keyValue = $parsed[$i];
			if (array_key_exists('indent', $keyValueInfoExpect)) {
				self::assertSame(
					$keyValueInfoExpect['indent'],
					$keyValue->getIndent(),
					sprintf('indent not same: keyValue %s', $i)
				);
			}
			if (array_key_exists('key', $keyValueInfoExpect)) {
				self::assertSame(
					$keyValueInfoExpect['key'],
					$keyValue->getKey(),
					sprintf('key not same: keyValue %s', $i)
				);
			}
			if (array_key_exists('pointerArrow', $keyValueInfoExpect)) {
				$actual = $keyValue->getPointerArrow();
				$keyValueInfoExpect['pointerArrow'] === true
					? self::assertSame(
						'=>',
						$tokens[$actual]['content'],
						sprintf('pointerArrow does not point to "=>": keyValue %s', $i)
					)
					: self::assertNull(
						$actual,
						sprintf('pointerArrow not null: keyValue %s', $i)
					);
			}
			if (array_key_exists('pointerComma', $keyValueInfoExpect)) {
				$actual = $keyValue->getPointerComma();
				$keyValueInfoExpect['pointerComma'] === true
					? self::assertSame(
						',',
						$tokens[$actual]['content'],
						sprintf('pointerComma does not point to ",": keyValue %s', $i)
					)
					: self::assertNull(
						$actual,
						sprintf('pointerComma not null: keyValue %s', $i)
					);
			}
			if (isset($keyValueInfoExpect['content']) === false) {
				continue;
			}
			self::assertSame(
				$keyValueInfoExpect['content'],
				$keyValue->getContent($phpcsFile),
				sprintf('raw content not same: keyValue %s', $i)
			);
			// build expected normalized content... this works for our example data
			$contentNormalizedExpect = trim($keyValueInfoExpect['content']);
			if (strpos($contentNormalizedExpect, ',') === false) {
				$contentNormalizedExpect .= ',';
			}
			self::assertSame(
				$contentNormalizedExpect,
				$keyValue->getContent($phpcsFile, true, null),
				sprintf('normalized content not same: keyValue %s', $i)
			);
		}
	}

	/**
	 * @dataProvider dataProvider
	 * @param array<string, mixed> $expect flags and various expected values for the given array
	 */
	public function testGetIndentation(File $phpcsFile, int $pointer, array $expect): void
	{
		if (array_key_exists('indentation', $expect) === false) {
			self::expectNotToPerformAssertions();
			return;
		}
		$parsed = ArrayHelper::parse($phpcsFile, $pointer);
		$indentation = ArrayHelper::getIndentation($parsed);
		self::assertSame($expect['indentation'], $indentation);
	}

	/**
	 * @dataProvider dataProvider
	 * @param array<string, mixed> $expect flags and various expected values for the given array
	 */
	public function testIsKeyed(File $phpcsFile, int $pointer, array $expect): void
	{
		if (array_key_exists('flags', $expect) === false) {
			self::expectNotToPerformAssertions();
			return;
		}
		$parsed = ArrayHelper::parse($phpcsFile, $pointer);
		$isKeyed = ArrayHelper::isKeyed($parsed);
		self::assertSame(in_array('keyed', $expect['flags'], true), $isKeyed);
	}

	/**
	 * @dataProvider dataProvider
	 * @param array<string, mixed> $expect flags and various expected values for the given array
	 */
	public function testIsKeyedAll(File $phpcsFile, int $pointer, array $expect): void
	{
		if (array_key_exists('flags', $expect) === false) {
			self::expectNotToPerformAssertions();
			return;
		}
		$parsed = ArrayHelper::parse($phpcsFile, $pointer);
		$isKeyedAll = ArrayHelper::isKeyedAll($parsed);
		self::assertSame(in_array('keyedAll', $expect['flags'], true), $isKeyedAll);
	}

	/**
	 * @dataProvider dataProvider
	 * @param array<string, mixed> $expect flags and various expected values for the given array
	 */
	public function testIsMultiLine(File $phpcsFile, int $pointer, array $expect): void
	{
		if (array_key_exists('flags', $expect) === false) {
			self::expectNotToPerformAssertions();
			return;
		}
		$isMultiLine = ArrayHelper::isMultiLine($phpcsFile, $pointer);
		self::assertSame(in_array('multi', $expect['flags'], true), $isMultiLine);
	}

	/**
	 * @dataProvider dataProvider
	 * @param array<string, mixed> $expect flags and various expected values for the given array
	 */
	public function testIsNotEmpty(File $phpcsFile, int $pointer, array $expect): void
	{
		if (array_key_exists('flags', $expect) === false) {
			self::expectNotToPerformAssertions();
			return;
		}
		$isNotEmpty = ArrayHelper::isNotEmpty($phpcsFile, $pointer);
		self::assertSame(in_array('notEmpty', $expect['flags'], true), $isNotEmpty);
	}

	/**
	 * @dataProvider dataProvider
	 * @param array<string, mixed> $expect flags and various expected values for the given array
	 */
	public function testIsSortedByKey(File $phpcsFile, int $pointer, array $expect): void
	{
		if (array_key_exists('flags', $expect) === false) {
			self::expectNotToPerformAssertions();
			return;
		}
		if (in_array('keyed', $expect['flags'], true) === false) {
			self::expectNotToPerformAssertions();
			return;
		}
		$parsed = ArrayHelper::parse($phpcsFile, $pointer);
		$isSortedByKey = ArrayHelper::isSortedByKey($parsed);
		self::assertSame(in_array('sorted', $expect['flags'], true), $isSortedByKey);
	}

	/**
	 * @dataProvider dataProvider
	 * @param array<string, mixed> $expect flags and various expected values for the given array
	 */
	public function testOpenClosePointers(File $phpcsFile, int $pointer, array $expect): void
	{
		if (array_key_exists('flags', $expect) === false) {
			self::expectNotToPerformAssertions();
			return;
		}
		$tokens = $phpcsFile->getTokens();
		$token = $tokens[$pointer];
		[$pointerOpener, $pointerCloser] = ArrayHelper::openClosePointers($token);
		$tokenOpener = $tokens[$pointerOpener];
		$tokenCloser = $tokens[$pointerCloser];
		$expect = in_array('short', $expect['flags'], true)
			? ['[', ']']
			: ['(', ')'];
		self::assertSame($expect, [$tokenOpener['content'], $tokenCloser['content']]);
	}

	public function dataProvider(): Generator
	{
		$files = $this->files();
		foreach ($files as $file => $fileArrays) {
			$phpcsFile = $this->getCodeSnifferFile($file);
			$arrayPointers = TokenHelper::findNextAll($phpcsFile, TokenHelper::$arrayTokenCodes, 0);
			foreach ($fileArrays as $k => $arrayInfoExpect) {
				$pointer = $arrayPointers[$k];

				yield basename($file) . ':' . $k => [
					$phpcsFile,
					$pointer,
					$arrayInfoExpect,
				];
			}
		}
	}

	/**
	 * Subset of files()... fileArrays containing keyValueInfo
	 */
	public function parseProvider(): Generator
	{
		$files = $this->files();
		foreach ($files as $file => $fileArrays) {
			$phpcsFile = $this->getCodeSnifferFile($file);
			$arrayPointers = TokenHelper::findNextAll($phpcsFile, TokenHelper::$arrayTokenCodes, 0);
			foreach ($fileArrays as $k => $arrayInfoExpect) {
				if (isset($arrayInfoExpect['keyValues']) === false) {
					continue;
				}
				$pointer = $arrayPointers[$k];

				yield basename($file) . ':' . $k => [
					$phpcsFile,
					$pointer,
					$arrayInfoExpect,
				];
			}
		}
	}

	/**
	 * @return array<string, array<int, array<string,
	 *    array<int, array<string, bool|string|null>|string>|int|string|null>>>
	 */
	protected function files(): array
	{
		return [
			__DIR__ . '/data/array/isNotEmpty.php' => [
				[
					'flags' => ['multi'],
					'indentation' => "\t",
				],
				[
					'flags' => ['multi', 'short'],
					'indentation' => "\t",
				],
				[
					'flags' => ['multi', 'notEmpty'],
					'indentation' => "\t",
				],
				[
					'flags' => ['multi', 'notEmpty', 'short'],
					'indentation' => "\t",
				],
			],
			__DIR__ . '/data/array/multiline1Keyed1Sorted1.php' => [
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'sorted'],
					'indentation' => "\t",
				],
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'sorted', 'short'],
					'indentation' => "\t\t",
				],
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'sorted', 'short'],
					'indentation' => "\t",
				],
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'sorted'],
					'indentation' => "\t\t",
				],
			],
			__DIR__ . '/data/array/multiline1Keyed1Sorted0.php' => [
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty'],
					'indentation' => "\t",
				],
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'short'],
					'indentation' => "\t\t",
				],
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'short'],
					'indentation' => "\t",
				],
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty'],
					'indentation' => "\t\t",
				],
			],
			__DIR__ . '/data/array/multiline0Keyed1Sorted1.php' => [
				[
					'flags' => ['keyed', 'keyedAll', 'notEmpty', 'sorted'],
					'indentation' => null,
				],
				[
					'flags' => ['keyed', 'keyedAll', 'notEmpty', 'sorted', 'short'],
					'indentation' => null,
				],
			],
			__DIR__ . '/data/array/multiline1Keyed0.php' => [
				[
					'flags' => ['multi', 'notEmpty'],
					'indentation' => "\t",
				],
				[
					'flags' => ['multi', 'notEmpty', 'short'],
					'indentation' => "\t\t",
				],
				[
					'flags' => ['multi', 'notEmpty', 'short'],
					'indentation' => "\t",
				],
				[
					'flags' => ['multi', 'notEmpty'],
					'indentation' => "\t\t",
				],
			],
			__DIR__ . '/data/array/multiline1Keyed1Comments.php' => [
				[
					'count' => 8,
					'flags' => ['keyed', 'multi', 'notEmpty', 'short'],
					'indentation' => "\t",
					'keyValues' => [
						[
							'content' => "	'b' => 'b val',  // comment\n"
								. "					 // more 'b' comment\n",
							'indent' => "\t",
							'key' => "'b'",
							'pointerArrow' => true,
						],
						[
							'content' => "	'a' . strtolower('param') => 'a val',  /* comment */\n",
							'indent' => "\t",
							'key' => "'a' . strtolower('param')",
							'pointerArrow' => true,
						],
						[
							'content' => "	// closure comment\n"
							. "	'closure' => static function (\$p1, \$p2) {\n"
							. "		return ['a2', 'b2'];\n"
							. "	},\n",
							'indent' => "\t",
							'key' => "'closure'",
							'pointerArrow' => true,
						],
						[
							'content' => "	'nested' => array(\n"
							. "		'b3' => 'b3 val',\n"
							. "		'a3' => 'a3 val',\n"
							. "	),\n",
							'indent' => "\t",
							'key' => "'nested'",
							'pointerArrow' => true,
						],
						[
							'content' => "	'arrow' => fn(\$x) => strtolower(\$x),\n",
							'indent' => "\t",
							'key' => "'arrow'",
							'pointerArrow' => true,
						],
						[
							'content' => "	'anonymous' => new class {\n"
							. "		public function log(\$msg)\n"
							. "		{\n"
							. "			return true;\n"
							. "		}\n"
							. "	},\n",
							'indent' => "\t",
							'key' => "'anonymous'",
							'pointerArrow' => true,
						],
						[
							'content' => "	'foo', ",
							'indent' => "\t",
							'key' => null,
							'pointerArrow' => false,
							'pointerComma' => true,
						],
						[
							'content' => "'bar'",
							'indent' => null,
							'key' => null,
							'pointerArrow' => false,
							'pointerComma' => false,
						],
					],
				],
				[
					// the array inside the closure
					'count' => 2,
					'flags' => ['notEmpty', 'short'],
					'indentation' => null,
					'keyValues' => [
						[
							'content' => "'a2', ",
							'indent' => null,
							'key' => null,
							'pointerArrow' => false,
							'pointerComma' => true,
						],
						[
							'content' => "'b2'",
							'indent' => null,
							'key' => null,
							'pointerArrow' => false,
							'pointerComma' => false,
						],
					],
				],
				[
					// the nested array
					'count' => 2,
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty'],
					'indentation' => "\t\t",
					'keyValues' => [
						[
							'content' => "		'b3' => 'b3 val',\n",
							'indent' => "\t\t",
							'key' => "'b3'",
							'pointerArrow' => true,
							'pointerComma' => true,
						],
						[
							'content' => "		'a3' => 'a3 val',\n",
							'indent' => "\t\t",
							'key' => "'a3'",
							'pointerArrow' => true,
							'pointerComma' => true,
						],
					],
				],
			],
			__DIR__ . '/data/array/commentBeforeCloser.php' => [
				[
					'keyValues' => [
						[
							'content' => "	'a' => 'a',\n",
						],
					],
				],
				[
					'keyValues' => [
						[
							'content' => "	'a' => 'a', // eol comment\n",
						],
					],
				],
				[
					'keyValues' => [
						[
							'content' => "	'a' => 'a', // eol comment\n"
								. "				// continued\n",
						],
					],
				],
			],
		];
	}

}
