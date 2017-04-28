<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;

class LongTypeHintsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_USED_LONG_TYPE_HINT = 'UsedLongTypeHint';

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
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $pointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $pointer)
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

					if ($suggestType !== null) {
						$phpcsFile->addError(sprintf(
							'Expected "%s" but found "%s" in %s annotation.',
							$suggestType,
							$type,
							$annotationName
						), $pointer, self::CODE_USED_LONG_TYPE_HINT);
					}
				}
			}
		}
	}

}
