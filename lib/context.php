<?php

namespace MSL\Context;

use MSL\Context;

/**
 * Stores a value in a context.
 */
function withValue(Context $ctx, mixed $key, mixed $value): Context
{
    return new Value($ctx, $key, $value);
}

/**
 * Returns a Context that always returns null
 */
function nil(): Context
{
    return Nil::instance();
}