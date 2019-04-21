<?php

/**
 * @param \Some\Exception $e
 * @throws \Exception
 */
function bar(\Some\Exception $e)
{
	try {
		throw new \Some\Other\Exception();
	} catch (\Some\Other\DifferentException $ex) {

	} catch (\Throwable $ex) {

	} catch (\Exception $ex) {

	} catch (\TypeError $ex) {

	} catch (\BarErrorX $ex) {
		throw new \Exception();
	}
}

class Lorem implements \Dolor, \Amet
{

}

class Ipsum extends \Bar
{

}

/**
 * @return \Lorem
 */
function getLorem(): \Lorem
{

}

/**
 * @method method1(string $parameter = \Lorem::class)
 * @method method2(array $parameter = [\Lorem::class => \Dolor::class], $parameter2)
 */
class ConstantExpression
{

}
