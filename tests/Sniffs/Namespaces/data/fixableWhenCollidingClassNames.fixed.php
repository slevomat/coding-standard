<?php declare(strict_types=1);

namespace App\Controller\Common;
use App\Process\UserClient\Edit;

abstract class AbstractUserEditController
{

	/**
	 * @var Edit|\App\Process\UserSuperAdmin\Edit
	 */
	protected $edit;

}
