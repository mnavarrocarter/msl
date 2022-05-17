<?php

namespace MSL\Fmt;

function sprintf(string $format, mixed ...$values): string
{
    return \sprintf($format, ...$values);
}