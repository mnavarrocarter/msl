<?php

namespace MSL\Str;

const TITLE_SEPARATORS = " \t\r\n\f\v";
const TRIM_CHARS = " \t\n\r\0\x0B";

function toUpper(string $string): string
{
   return \strtoupper($string);
}

function toLower(string $string): string
{
    return \strtolower($string);
}

function toTitle(string $string, string $separators = TITLE_SEPARATORS): string
{
    return \ucwords($string, $separators);
}

/**
 * @param string $string The string to perform the replacements in
 * @param string $search The string to search
 * @param string $replace The replacement string
 * @return string
 */
function replace(string $string, string $search, string $replace): string
{
    return \str_replace($search, $replace, $string);
}

/**
 * @param string $string
 * @param string $substring
 * @return int
 */
function index(string $string, string $substring): int
{
    $pos = \strpos($string, $substring);
    if (!is_int($pos)) {
        return -1;
    }
    return $pos;
}

/**
 * @param string $string
 * @param string $chars
 * @return string
 */
function trim(string $string, string $chars = TRIM_CHARS): string
{
    return \trim($string, $chars);
}

/**
 * @return string[]
 */
function split(string $string, string $separator = '', int $limit = null): array
{
    if ($limit !== null) {
        return \explode($separator, $string, $limit);
    }

    return \explode($separator, $string);
}

/**
 * @param string[] $array
 * @param string $glue
 * @return string
 */
function join(array $array, string $glue = ''): string
{
    return \implode($glue, $array);
}
