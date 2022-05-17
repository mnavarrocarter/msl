<?php

namespace MSL\Str;

const SEPARATOR = " \t\r\n\f\v";

function toUpper(string $string): string
{
   return \strtoupper($string);
}

function toLower(string $string): string
{
    return \strtolower($string);
}

function toTitle(string $string, string $separators = SEPARATOR): string
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

