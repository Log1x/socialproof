<?php

namespace SocialProof;

use SocialProof\Builders\ProviderBuilder;

class SocialProof
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
     * Create a new ProviderBuilder instance.
     *
     * @return \SocialProof\Builders\ProviderBuilder
     */
    public static function social()
    {
        return (new ProviderBuilder());
    }
}
