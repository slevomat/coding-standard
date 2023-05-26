<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use function sprintf;
use function strtolower;
use const T_DOC_COMMENT_OPEN_TAG;

class DisallowMixedTypeHintSniff implements Sniff
{

	public const CODE_DISALLOWED_MIXED_TYPE_HINT = 'DisallowedMixedTypeHint';

	private const NAME = 'SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $docCommentOpenPointer
	 */
	public function process(File $phpcsFile, $docCommentOpenPointer): void
	{
		if (SuppressHelper::isSniffSuppressed(
			$phpcsFile,
			$docCommentOpenPointer,
			$this->getSniffName(self::CODE_DISALLOWED_MIXED_TYPE_HINT)
		)) {
			return;
		}

		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer);

		foreach ($annotations as $annotation) {
			/** @var list<IdentifierTypeNode> $identifierTypeNodes */
			$identifierTypeNodes = AnnotationHelper::getAnnotationNodesByType($annotation->getNode(), IdentifierTypeNode::class);

			foreach ($identifierTypeNodes as $typeHintNode) {
				$typeHint = $typeHintNode->name;

				if (strtolower($typeHint) !== 'mixed') {
					continue;
				}

				$phpcsFile->addError(
					'Usage of "mixed" type hint is disallowed.',
					$annotation->getStartPointer(),
					self::CODE_DISALLOWED_MIXED_TYPE_HINT
				);
			}
		}
	}

	private function getSniffName(string $sniffName): string
	{
		return sprintf('%s.%s', self::NAME, $sniffName);
	}

}
