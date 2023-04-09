<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use function array_key_exists;
use function count;
use function in_array;
use function sprintf;
use function strpos;
use function trim;

class ArrayHelperTest extends TestCase
{

	/**
	 * @dataProvider dataKeyValues
	 * @param array{keyValues: array<string, mixed>} $expect
	 */
	public function testParse(string $file, int $arrayPointerNo, array $expect): void
	{
		$phpcsFile = $this->getCodeSnifferFile($file);
		$arrayPointers = TokenHelper::findNextAll($phpcsFile, TokenHelper::$arrayTokenCodes, 0);

		$parsed = ArrayHelper::parse($phpcsFile, $arrayPointers[$arrayPointerNo]);
		$tokens = $phpcsFile->getTokens();

		foreach ($parsed as $keyValue) {
			/** @phpstan-var mixed $keyValue phpstan making assumption based on phpdoc */
			self::assertInstanceOf(ArrayKeyValue::class, $keyValue);
		}

		self::assertCount(count($expect['keyValues']), $parsed);

		foreach ($expect['keyValues'] as $i => $keyValueInfoExpect) {
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

			if (!array_key_exists('content', $keyValueInfoExpect)) {
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
				$keyValue->getContent($phpcsFile, true),
				sprintf('normalized content not same: keyValue %s', $i)
			);
		}
	}

	/**
	 * @dataProvider dataFlagsAndIndentation
	 * @dataProvider dataKeyValues
	 * @param array{indentation: string|null} $expect
	 */
	public function testGetIndentation(string $file, int $arrayPointerNo, array $expect): void
	{
		$phpcsFile = $this->getCodeSnifferFile($file);
		$arrayPointers = TokenHelper::findNextAll($phpcsFile, TokenHelper::$arrayTokenCodes, 0);

		$parsed = ArrayHelper::parse($phpcsFile, $arrayPointers[$arrayPointerNo]);
		$indentation = ArrayHelper::getIndentation($parsed);

		self::assertSame($expect['indentation'], $indentation);
	}

	/**
	 * @dataProvider dataFlagsAndIndentation
	 * @dataProvider dataKeyValues
	 * @param array{flags: string[]} $expect
	 */
	public function testIsKeyed(string $file, int $arrayPointerNo, array $expect): void
	{
		$phpcsFile = $this->getCodeSnifferFile($file);
		$arrayPointers = TokenHelper::findNextAll($phpcsFile, TokenHelper::$arrayTokenCodes, 0);

		$parsed = ArrayHelper::parse($phpcsFile, $arrayPointers[$arrayPointerNo]);
		$isKeyed = ArrayHelper::isKeyed($parsed);

		self::assertSame(in_array('keyed', $expect['flags'], true), $isKeyed);
	}

	/**
	 * @dataProvider dataFlagsAndIndentation
	 * @dataProvider dataKeyValues
	 * @param array{flags: string[]} $expect
	 */
	public function testIsKeyedAll(string $file, int $arrayPointerNo, array $expect): void
	{
		$phpcsFile = $this->getCodeSnifferFile($file);
		$arrayPointers = TokenHelper::findNextAll($phpcsFile, TokenHelper::$arrayTokenCodes, 0);

		$parsed = ArrayHelper::parse($phpcsFile, $arrayPointers[$arrayPointerNo]);
		$isKeyedAll = ArrayHelper::isKeyedAll($parsed);

		self::assertSame(in_array('keyedAll', $expect['flags'], true), $isKeyedAll);
	}

	/**
	 * @dataProvider dataFlagsAndIndentation
	 * @dataProvider dataKeyValues
	 * @param array{flags: string[]} $expect
	 */
	public function testIsMultiLine(string $file, int $arrayPointerNo, array $expect): void
	{
		$phpcsFile = $this->getCodeSnifferFile($file);
		$arrayPointers = TokenHelper::findNextAll($phpcsFile, TokenHelper::$arrayTokenCodes, 0);

		$isMultiLine = ArrayHelper::isMultiLine($phpcsFile, $arrayPointers[$arrayPointerNo]);
		self::assertSame(in_array('multi', $expect['flags'], true), $isMultiLine);
	}

	/**
	 * @dataProvider dataFlagsAndIndentation
	 * @dataProvider dataKeyValues
	 * @param array{flags: string[]} $expect
	 */
	public function testIsNotEmpty(string $file, int $arrayPointerNo, array $expect): void
	{
		$phpcsFile = $this->getCodeSnifferFile($file);
		$arrayPointers = TokenHelper::findNextAll($phpcsFile, TokenHelper::$arrayTokenCodes, 0);

		$isNotEmpty = ArrayHelper::isNotEmpty($phpcsFile, $arrayPointers[$arrayPointerNo]);

		self::assertSame(in_array('notEmpty', $expect['flags'], true), $isNotEmpty);
	}

	/**
	 * @dataProvider dataFlagsAndIndentation
	 * @dataProvider dataKeyValues
	 * @param array{flags: string[]} $expect
	 */
	public function testIsSortedByKey(string $file, int $arrayPointerNo, array $expect): void
	{
		if (!in_array('keyed', $expect['flags'], true)) {
			self::expectNotToPerformAssertions();
			return;
		}

		$phpcsFile = $this->getCodeSnifferFile($file);
		$arrayPointers = TokenHelper::findNextAll($phpcsFile, TokenHelper::$arrayTokenCodes, 0);

		$parsed = ArrayHelper::parse($phpcsFile, $arrayPointers[$arrayPointerNo]);
		$isSortedByKey = ArrayHelper::isSortedByKey($parsed);

		self::assertSame(in_array('sorted', $expect['flags'], true), $isSortedByKey);
	}

	/**
	 * @dataProvider dataFlagsAndIndentation
	 * @dataProvider dataKeyValues
	 * @param array{flags: string[]} $expect
	 */
	public function testOpenClosePointers(string $file, int $arrayPointerNo, array $expect): void
	{
		$phpcsFile = $this->getCodeSnifferFile($file);
		$arrayPointers = TokenHelper::findNextAll($phpcsFile, TokenHelper::$arrayTokenCodes, 0);

		$tokens = $phpcsFile->getTokens();
		[$pointerOpener, $pointerCloser] = ArrayHelper::openClosePointers($tokens[$arrayPointers[$arrayPointerNo]]);

		$expect = in_array('short', $expect['flags'], true)
			? ['[', ']']
			: ['(', ')'];

		self::assertSame($expect, [$tokens[$pointerOpener]['content'], $tokens[$pointerCloser]['content']]);
	}

	/**
	 * @return array<int, array{0: string, 1: int, 2: array{flags: string[], indentation: string|null}}>
	 */
	public static function dataFlagsAndIndentation(): array
	{
		return [
			[
				__DIR__ . '/data/array/isNotEmpty.php',
				0,
				[
					'flags' => ['multi'],
					'indentation' => "\t",
				],
			],
			[
				__DIR__ . '/data/array/isNotEmpty.php',
				1,
				[
					'flags' => ['multi', 'short'],
					'indentation' => "\t",
				],
			],
			[
				__DIR__ . '/data/array/isNotEmpty.php',
				2,
				[
					'flags' => ['multi', 'notEmpty'],
					'indentation' => "\t",
				],
			],
			[
				__DIR__ . '/data/array/isNotEmpty.php',
				3,
				[
					'flags' => ['multi', 'notEmpty', 'short'],
					'indentation' => "\t",
				],
			],
			[
				__DIR__ . '/data/array/multiline1Keyed1Sorted1.php',
				0,
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'sorted'],
					'indentation' => "\t",
				],
			],
			[
				__DIR__ . '/data/array/multiline1Keyed1Sorted1.php',
				1,
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'sorted', 'short'],
					'indentation' => "\t\t",
				],
			],
			[
				__DIR__ . '/data/array/multiline1Keyed1Sorted1.php',
				2,
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'sorted', 'short'],
					'indentation' => "\t",
				],
			],
			[
				__DIR__ . '/data/array/multiline1Keyed1Sorted1.php',
				3,
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'sorted'],
					'indentation' => "\t\t",
				],
			],
			[
				__DIR__ . '/data/array/multiline1Keyed1Sorted0.php',
				0,
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty'],
					'indentation' => "\t",
				],
			],
			[
				__DIR__ . '/data/array/multiline1Keyed1Sorted0.php',
				1,
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'short'],
					'indentation' => "\t\t",
				],
			],
			[
				__DIR__ . '/data/array/multiline1Keyed1Sorted0.php',
				2,
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'short'],
					'indentation' => "\t",
				],
			],
			[
				__DIR__ . '/data/array/multiline1Keyed1Sorted0.php',
				3,
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty'],
					'indentation' => "\t\t",
				],
			],
			[
				__DIR__ . '/data/array/multiline0Keyed1Sorted1.php',
				0,
				[
					'flags' => ['keyed', 'keyedAll', 'notEmpty', 'sorted'],
					'indentation' => null,
				],
			],
			[
				__DIR__ . '/data/array/multiline0Keyed1Sorted1.php',
				1,
				[
					'flags' => ['keyed', 'keyedAll', 'notEmpty', 'sorted', 'short'],
					'indentation' => null,
				],
			],
			[
				__DIR__ . '/data/array/multiline1Keyed0.php',
				0,
				[
					'flags' => ['multi', 'notEmpty'],
					'indentation' => "\t",
				],
			],
			[
				__DIR__ . '/data/array/multiline1Keyed0.php',
				1,
				[
					'flags' => ['multi', 'notEmpty', 'short'],
					'indentation' => "\t\t",
				],
			],
			[
				__DIR__ . '/data/array/multiline1Keyed0.php',
				2,
				[
					'flags' => ['multi', 'notEmpty', 'short'],
					'indentation' => "\t",
				],
			],
			[
				__DIR__ . '/data/array/multiline1Keyed0.php',
				3,
				[
					'flags' => ['multi', 'notEmpty'],
					'indentation' => "\t\t",
				],
			],
		];
	}

	/**
	 * @return array<int, array{0: string, 1: int, 2: array{flags: string[], indentation: string|null, keyValues: array<int, array<string, mixed>>}}>
	 */
	public static function dataKeyValues(): array
	{
		return [
			[
				__DIR__ . '/data/array/multiline1Keyed1Comments.php',
				0,
				[
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
			],
			[
				__DIR__ . '/data/array/multiline1Keyed1Comments.php',
				1,
				[
					// the array inside the closure
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
			],
			[
				__DIR__ . '/data/array/multiline1Keyed1Comments.php',
				2,
				[
					// the nested array
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
			[
				__DIR__ . '/data/array/commentBeforeCloser.php',
				0,
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'short', 'sorted'],
					'indentation' => "\t",
					'keyValues' => [
						[
							'content' => "	'a' => 'a',\n",
						],
					],
				],
			],
			[
				__DIR__ . '/data/array/commentBeforeCloser.php',
				1,
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'short', 'sorted'],
					'indentation' => "\t",
					'keyValues' => [
						[
							'content' => "	'a' => 'a', // eol comment\n",
						],
					],
				],
			],
			[
				__DIR__ . '/data/array/commentBeforeCloser.php',
				2,
				[
					'flags' => ['keyed', 'keyedAll', 'multi', 'notEmpty', 'short', 'sorted'],
					'indentation' => "\t",
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
