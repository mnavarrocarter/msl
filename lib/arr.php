<?php

namespace MSL\Arr;

function map(array $array, callable $func): array
{
    return \array_map($func, $array);
}

function filter(array $array, callable $func): array
{
    return \array_filter($array, $func);
}

function push(array &$array, mixed $element): int
{
    return array_push($array, $element);
}