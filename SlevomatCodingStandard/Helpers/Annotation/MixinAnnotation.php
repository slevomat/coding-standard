<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use PHPStan\PhpDocParser\Ast\PhpDoc\MixinTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use function sprintf;

/**
 * @internal
 */
class MixinAnnotation extends Annotation
{

	/** @var MixinTagValueNode|null */
	private $contentNode;

	public function __construct(string $name, int $startPointer, int $endPointer, ?string $content, ?MixinTagValueNode $contentNode)
	{
		if ($name !== '@mixin') {
			throw new InvalidArgumentException(sprintf('Unsupported annotation %s.', $name));
		}

		parent::__construct($name, $startPointer, $endPointer, $content);

		$this->contentNode = $contentNode;
	}

	public function isInvalid(): bool
	{
		return $this->contentNode === null;
	}

	public function getContentNode(): MixinTagValueNode
	{
		$this->errorWhenInvalid();

		return $this->contentNode;
	}

	public function hasDescription(): bool
	{
		return $this->getDescription() !== null;
	}

	public function getDescription(): ?string
	{
		$this->errorWhenInvalid();

		return $this->contentNode->description !== '' ? $this->contentNode->description : null;
	}

	/**
	 * @return GenericTypeNode|IdentifierTypeNode
	 */
	public function getType(): TypeNode
	{
		$this->errorWhenInvalid();

		/** @var GenericTypeNode|IdentifierTypeNode $type */
		$type = $this->contentNode->type;
		return $type;
	}

	public function print(): string
	{
		return sprintf('%s %s', $this->name, AnnotationHelper::getPhpDocPrinter()->print($this->contentNode));
	}

}
