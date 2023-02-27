<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Strings;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use UnexpectedValueException;
use function preg_match;
use function sprintf;
use const T_DOUBLE_QUOTED_STRING;
use const T_HEREDOC;

class DisallowVariableParsingSniff implements Sniff
{

	public const CODE_DISALLOWED_DOLLAR_CURLY_SYNTAX = 'DisallowedDollarCurlySyntax';

	public const CODE_DISALLOWED_CURLY_DOLLAR_SYNTAX = 'DisallowedCurlyDollarSyntax';

	public const CODE_DISALLOWED_SIMPLE_SYNTAX = 'DisallowedSimpleSyntax';

	private const DOLLAR_CURLY_SYNTAX_PATTERN = '~\${[\w\[\]]+}~';
	private const CURLY_DOLLAR_SYNTAX_PATTERN = '~{\$[\w\[\]\->]+}~';
	private const SIMPLE_SYNTAX_PATTERN = '~(?<!{)\$[\w\[\]\->]+(?!})~';

	/** @var bool */
	public $disallowDollarCurlySyntax = true;

	/** @var bool */
	public $disallowCurlyDollarSyntax = false;

	/** @var bool */
	public $disallowSimpleSyntax = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_DOUBLE_QUOTED_STRING,
			T_HEREDOC,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $stringPointer
	 */
	public function process(File $phpcsFile, $stringPointer): void
	{
		if (!$this->disallowDollarCurlySyntax && !$this->disallowCurlyDollarSyntax && !$this->disallowSimpleSyntax) {
			throw new UnexpectedValueException('No option is set.');
		}

		$tokens = $phpcsFile->getTokens();
		$tokenContent = $tokens[$stringPointer]['content'];

		// Cover strings where ${...} syntax is used
		if ($this->disallowDollarCurlySyntax && preg_match(self::DOLLAR_CURLY_SYNTAX_PATTERN, $tokenContent, $invalidFragments) === 1) {
			foreach ($invalidFragments as $fragment) {
				$phpcsFile->addError(
					sprintf(
						'Using variable syntax "${...}" inside string is disallowed as syntax "${...}" is deprecated as of PHP 8.2, found "%s".',
						$fragment
					),
					$stringPointer,
					self::CODE_DISALLOWED_DOLLAR_CURLY_SYNTAX
				);
			}
		}

		// Cover strings where {$...} syntax is used
		if ($this->disallowCurlyDollarSyntax && preg_match(self::CURLY_DOLLAR_SYNTAX_PATTERN, $tokenContent, $invalidFragments) === 1) {
			foreach ($invalidFragments as $fragment) {
				$phpcsFile->addError(
					sprintf(
						'Using variable syntax "{$...}" inside string is disallowed, found "%s".',
						$fragment
					),
					$stringPointer,
					self::CODE_DISALLOWED_CURLY_DOLLAR_SYNTAX
				);
			}
		}

		// Cover strings where $... syntax is used
		// phpcs:disable SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
		if ($this->disallowSimpleSyntax && preg_match(self::SIMPLE_SYNTAX_PATTERN, $tokenContent, $invalidFragments) === 1) {
			foreach ($invalidFragments as $fragment) {
				$phpcsFile->addError(
					sprintf(
						'Using variable syntax "$..." inside string is disallowed, found "%s".',
						$fragment
					),
					$stringPointer,
					self::CODE_DISALLOWED_SIMPLE_SYNTAX
				);
			}
		}
	}

}
