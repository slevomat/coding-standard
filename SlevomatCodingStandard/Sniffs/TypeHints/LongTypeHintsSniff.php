<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class LongTypeHintsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_USED_LONG_TYPE_HINT = 'UsedLongTypeHint';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_FUNCTION,
			T_VARIABLE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $pointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $pointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$pointer]['code'] === T_FUNCTION) {
			$allAnnotations = ['@param' => FunctionHelper::getParametersAnnotations($phpcsFile, $pointer)];

			$return = FunctionHelper::findReturnAnnotation($phpcsFile, $pointer);
			if ($return !== null) {
				$allAnnotations['@return'] = [$return];
			}
		} else {
			if (!PropertyHelper::isProperty($phpcsFile, $pointer)) {
				return;
			}

			$allAnnotations = ['@var' => AnnotationHelper::getAnnotationsByName($phpcsFile, $pointer, '@var')];
		}

		foreach ($allAnnotations as $annotationName => $annotations) {
			foreach ($annotations as $annotation) {
				if ($annotation->getContent() === null) {
					continue;
				}

				$types = preg_split('~\\s+~', $annotation->getContent())[0];
				foreach (explode('|', $types) as $type) {
					$type = strtolower(trim($type, '[]'));
					$suggestType = null;
					if ($type === 'integer') {
						$suggestType = 'int';
					} elseif ($type === 'boolean') {
						$suggestType = 'bool';
					}

					if ($suggestType === null) {
						continue;
					}

					$fix = $phpcsFile->addFixableError(sprintf(
						'Expected "%s" but found "%s" in %s annotation.',
						$suggestType,
						$type,
						$annotationName
					), $annotation->getStartPointer(), self::CODE_USED_LONG_TYPE_HINT);

					if (!$fix) {
						continue;
					}

					/** @var int $docCommentOpenPointer */
					$docCommentOpenPointer = DocCommentHelper::findDocCommentOpenToken($phpcsFile, $pointer);
					$docCommentClosePointer = $tokens[$docCommentOpenPointer]['comment_closer'];

					$phpcsFile->fixer->beginChangeset();
					for ($i = $docCommentOpenPointer; $i <= $docCommentClosePointer; $i++) {
						$phpcsFile->fixer->replaceToken($i, '');
					}

					$docComment = TokenHelper::getContent($phpcsFile, $docCommentOpenPointer, $docCommentClosePointer);

					$fixedDocComment = preg_replace_callback('~((?:@(?:var|param|return)\\s+)|\|)' . preg_quote($type, '~') . '(?=\\s|\||\[)~i', function (array $matches) use ($suggestType): string {
						return $matches[1] . $suggestType;
					}, $docComment);

					$phpcsFile->fixer->addContent($docCommentOpenPointer, $fixedDocComment);

					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}

}
