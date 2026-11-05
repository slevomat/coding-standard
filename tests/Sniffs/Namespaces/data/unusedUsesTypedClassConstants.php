<?php // lint >= 8.3

namespace App;

use App\Codes\Code;
use App\Contracts\ErrorCodeInterface;
use App\Contracts\Nullable;
use App\Contracts\Union;

final class C
{

	public const ErrorCodeInterface CODE = Code::X;

	public const ?Nullable NULLABLE = null;

	public const int|Union UNION = 1;

}
