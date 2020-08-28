<?php

class Whatever
{
	public function __construct()
	{
		$this->doAnything('true', false, 'very looooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong parameter');

		sprintf('%s', 'very looooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong parameter');

		return new self('very looooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong parameter');
	}
}

\printf('%s', 'very looooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong parameter');

$array = array_merge([], array_map(function (): string {return 'very looooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong value';}, []));

$array2 = array_merge(
	[],
	array_map(function (): string {return 'very looooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong value';}, [])
);

$array3 = array_merge(['very looooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong parameter'], array_map(
	function (): string {return 'value';},
	[]
));

doSomething('anything') ? true : doSomethingElse('very looooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong parameter');

in_array(strtolower('striiiiiiiiiiiiiiiiiiiiiing'), ['true', 'false', 'class-string', 'trait-string', 'callable-string', 'numeric-string'], true);

$something = sprintf('%s', 'very looooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong parameter') + get_include_path();

function ($phpcsFile, $pointer) {
	return Whatever::doSomething($phpcsFile, sprintf('annotations-%d', $pointer), static function () use ($phpcsFile, $pointer): array {
	});
};

class Nothing
{
	public function commit($message, $originalQueueName, $delayedQueueName, $nextAttemptTime)
	{
		$this->doNowOrAfterCommit(function () use ($message, $originalQueueName, $delayedQueueName, $nextAttemptTime): void {
			$this->instantQueueMessageProducer->sendDelayedMessage($message, $originalQueueName, $delayedQueueName, $nextAttemptTime);
		});
	}
}

function ($text) {
	return sprintf(_('very looooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong parameter'), $text);
};

$a = _('very loooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong parameter');
