<?php

namespace SocialProof\Providers;

use SocialProof\Interfaces\ProviderInterface,
    SocialProof\Exceptions\ProviderException,
    SocialProof\Utilities\Utilities;

use GuzzleHttp\Client,
    GuzzleHttp\Psr7\Request,
    GuzzleHttp\Exception\RequestException,
    Psr\Http\Message\ResponseInterface;

class Instagram implements ProviderInterface
{
    /**
     * Default API for this provider.
     *
     * @var string
     */
    protected $api = 'https://api.instagram.com';

    /**
     * Default endpoint for this provider.
     *
     * @var string
     */
    protected $endpoint = '/v1/users/';

    /**
     * Default username for this provider.
     *
     * @var string
     */
    protected $username = 'self';

    /**
     * Credentials required for this provider.
     *
     * @var array
     */
    protected $credentials = [
        'token'
    ];

    /**
     * Client
     *
     * {@inheritDoc}
     */
    public function client($config)
    {
        return new Client([
            'base_uri' => $config->api,
            'timeout'  => $config->timeout
        ]);
    }

    /**
     * Instagram
     *
     * {@inheritDoc}
     */
    public function get($credentials, $config)
    {
        if (Utilities::compare($this->credentials, $credentials)) {
            throw new ProviderException(
                'Missing credentials for this provider.'
            );
        }

        $config->api = $config->api ?? $this->api;
        $config->endpoint = $config->endpoint ?? $this->endpoint;

        $credentials->username = $credentials->username ?? $this->username;

        $instagram = $this->client($config)->getAsync($credentials->username, [
            'query' => ['access_token' => $credentials->token]
        ])->then(
            function (ResponseInterface $response) use ($config) {
                $response = json_decode($response->getBody()->getContents());
                return !empty($response->data->counts->followed_by) ? intval($response->data->counts->followed_by) : $config->default;
            },

            function (RequestException $error) use ($config) {
                return $config->debug ? $error->getMessage() : $config->default;
            }
        );

        return $instagram->wait();
    }
}
