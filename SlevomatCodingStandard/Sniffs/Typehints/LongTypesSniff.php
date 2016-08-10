<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Typehints;

use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class LongTypesSniff implements \PHP_CodeSniffer_Sniff
{

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_FUNCTION,
			T_VARIABLE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Typehints.TypeHintDeclaration.missingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $pointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $pointer)
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$pointer]['code'] === T_FUNCTION) {
			$annotations = FunctionHelper::getParametersAnnotations($phpcsFile, $pointer);

			$return = FunctionHelper::findReturnAnnotation($phpcsFile, $pointer);
			if ($return !== null) {
				$annotations[] = $return;
			}
		} elseif ($tokens[$pointer]['code'] === T_VARIABLE) {
			$propertyDeterminingPointer = TokenHelper::findPreviousExcluding(
				$phpcsFile,
				array_merge([T_STATIC], TokenHelper::$ineffectiveTokenCodes),
				$pointer - 1
			);
			if (!in_array($tokens[$propertyDeterminingPointer]['code'], [
				T_PRIVATE,
				T_PROTECTED,
				T_PUBLIC,
				T_VAR,
				], true)
			) {
				return;
			}

			$annotations = AnnotationHelper::getAnnotationsByName($phpcsFile, $pointer, '@var');
		} else {
			return;
		}

		foreach ($annotations as $annotation) {
			$annotationParts = preg_split('~\\s+~', $annotation);
			if (count($annotationParts) === 0) {
				continue;
			}

			$types = $annotationParts[0];
			foreach (explode('|', $types) as $type) {
				$type = strtolower(trim($type, '[]'));
				$suggestType = null;
				if ($type === 'integer') {
					$suggestType = 'int';
				} elseif ($type === 'boolean') {
					$suggestType = 'bool';
				}

				if ($suggestType !== null) {
					$phpcsFile->addError(sprintf(
						'Expected "%s" but found "%s" in typehint annotation',
						$suggestType,
						$type
					), $pointer);
				}
			}
		}
	}

}
