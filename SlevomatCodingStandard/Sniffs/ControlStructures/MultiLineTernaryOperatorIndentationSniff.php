<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

/**
 * Enforces that multiline ternary operators ("?" and ":") are placed at the start of continuation lines
 * and indented exactly one level deeper than the line where the ternary condition begins, followed by a single space.
 *
 * Short ternary operators ("?:") are ignored. Also suppresses conflicting indentation checks from
 * Generic.WhiteSpace.ScopeIndent and Squiz.Functions.MultiLineFunctionDeclaration on ternary continuation lines.
 */
class MultiLineTernaryOperatorIndentationSniff extends DisallowTrailingMultiLineTernaryOperatorSniff
{

	/**
	 * Error code reported when a multiline ternary operator ("?" or ":") is trailing at the end of a line
	 * or has invalid leading indentation or spacing.
	 */
	public const CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION = 'InvalidMultiLineTernaryOperatorIndentation';

	/**
	 * Alias for CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION when a ternary operator is placed at the end of a line.
	 */
	public const CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED = self::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION;

	/**
	 * Returns the error code reported when a multiline ternary operator is trailing or has invalid indentation.
	 *
	 * @return string The sniff error code.
	 */
	protected function getErrorCode(): string
	{
		return self::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION;
	}

}
