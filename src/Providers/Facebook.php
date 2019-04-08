<?php

namespace SocialProof\Providers;

use SocialProof\Interfaces\ProviderInterface,
    SocialProof\Exceptions\ProviderException,
    SocialProof\Utilities\Utilities;

use GuzzleHttp\Client,
    GuzzleHttp\Psr7\Request,
    GuzzleHttp\Exception\RequestException,
    Psr\Http\Message\ResponseInterface;

class Facebook implements ProviderInterface
{
    /**
     * Default API for this provider.
     *
     * @var string
     */
    protected $api = 'https://graph.facebook.com';

    /**
     * Default endpoint for this provider.
     *
     * @var string
     */
    protected $endpoint = '/v2.7/';

    /**
     * Credentials required for this provider.
     *
     * @var array
     */
    protected $credentials = [
        'username',
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
     * Facebook
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

        $facebook = $this->client($config)->getAsync($config->endpoint . $credentials->username, [
            'query' => [
                'fields'       => 'fan_count',
                'access_token' => $credentials->token
            ]
        ])->then(
            function (ResponseInterface $response) use ($config) {
                $response = json_decode($response->getBody()->getContents());
                return !empty($response->fan_count) ? intval($response->fan_count) : $config->default;
            },

            function (RequestException $error) use ($config) {
                return $config->debug ? $error->getMessage() : $config->default;
            }
        );

        return $facebook->wait();
    }
}
