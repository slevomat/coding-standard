<?php // lint >= 7.4

function validate($regex, $value, $name)
{
        return !preg_match("/$regex/", (string) $value) ? "недопустимый формат, поле '$name'" : null;
}
