<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Strings;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use UnexpectedValueException;
use function count;
use function in_array;
use function is_array;
use function sprintf;
use function strpos;
use function token_get_all;
use const T_DOUBLE_QUOTED_STRING;
use const T_HEREDOC;
use const T_VARIABLE;

class DisallowVariableParsingSniff implements Sniff
{

	public const CODE_DISALLOWED_DOLLAR_CURLY_SYNTAX = 'DisallowedDollarCurlySyntax';

	public const CODE_DISALLOWED_CURLY_DOLLAR_SYNTAX = 'DisallowedCurlyDollarSyntax';

	public const CODE_DISALLOWED_SIMPLE_SYNTAX = 'DisallowedSimpleSyntax';

	public bool $disallowDollarCurlySyntax = true;

	public bool $disallowCurlyDollarSyntax = false;

	public bool $disallowSimpleSyntax = false;

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

		if (strpos($tokenContent, '$') === false) {
			return;
		}

		$stringTokens = $tokens[$stringPointer]['code'] === T_HEREDOC
			? token_get_all('<?php "' . $tokenContent . '"')
			: token_get_all('<?php ' . $tokenContent);

		for ($i = 0; $i < count($stringTokens); $i++) {
			$stringToken = $stringTokens[$i];

			if (!is_array($stringToken)) {
				continue;
			}

			if ($this->disallowDollarCurlySyntax && $this->getTokenContent($stringToken) === '${') {
				$usedVariable = $stringToken[1];

				for ($j = $i + 1; $j < count($stringTokens); $j++) {
					$usedVariable .= $this->getTokenContent($stringTokens[$j]);

					if ($this->getTokenContent($stringTokens[$j]) === '}') {
						$phpcsFile->addError(
							sprintf(
								'Using variable syntax "${...}" inside string is disallowed as syntax "${...}" is deprecated as of PHP 8.2, found "%s".',
								$usedVariable,
							),
							$stringPointer,
							self::CODE_DISALLOWED_DOLLAR_CURLY_SYNTAX,
						);

						break;
					}
				}
			} elseif ($stringToken[0] === T_VARIABLE) {
				if ($this->disallowCurlyDollarSyntax && $this->getTokenContent($stringTokens[$i - 1]) === '{') {
					$usedVariable = $stringToken[1];

					for ($j = $i + 1; $j < count($stringTokens); $j++) {
						$stringTokenContent = $this->getTokenContent($stringTokens[$j]);
						if ($stringTokenContent === '}') {
							break;
						}

						$usedVariable .= $stringTokenContent;
					}

					$phpcsFile->addError(
						sprintf(
							'Using variable syntax "{$...}" inside string is disallowed, found "{%s}".',
							$usedVariable,
						),
						$stringPointer,
						self::CODE_DISALLOWED_CURLY_DOLLAR_SYNTAX,
					);
				} elseif ($this->disallowSimpleSyntax) {
					$error = true;

					for ($j = $i - 1; $j >= 0; $j--) {
						$stringTokenContent = $this->getTokenContent($stringTokens[$j]);

						if (in_array($stringTokenContent, ['{', '${'], true)) {
							$error = false;
							break;
						}

						if ($stringTokenContent === '}') {
							break;
						}
					}

					if ($error) {
						$phpcsFile->addError(
							sprintf(
								'Using variable syntax "$..." inside string is disallowed, found "%s".',
								$this->getTokenContent($stringToken),
							),
							$stringPointer,
							self::CODE_DISALLOWED_SIMPLE_SYNTAX,
						);
					}
				}
			}
		}
	}

	/**
	 * @param array{0: int, 1: string}|string $token
	 */
	private function getTokenContent($token): string
	{
		return is_array($token) ? $token[1] : $token;
	}

}
