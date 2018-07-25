<?php

namespace SocialProof\Interfaces;

interface ProviderInterface
{
    /**
     * Get the client used for the provider API.
     *
     * @param  object $config
     * @return mixed
     */
    public function client($config);

    /**
     * Get the provider.
     *
     * @param  object $credentials
     * @param  object $config
     * @return mixed
     */
     public function get($credentials, $config);
}
