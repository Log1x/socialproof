<?php

namespace SocialProof\Utilities;

class Utilities
{
    /**
     * Private constructor to prevent instantiation.
     *
     * @codeCoverageIgnore The class cannot be instantiated.
     */
    private function __construct()
    {
        //
    }

    /**
     * Compares the keys between two arrays and returns the values
     * in $a that are not present in $b.
     *
     * If passed an object, the object is casted to an array and
     * flattened with array_keys().
     *
     * @param  mixed $a
     * @param  mixed $b
     * @return void
     */
    public static function compare($a, $b)
    {
        return array_diff_key(array_keys((array) $a), array_keys((array) $b));
    }
}
