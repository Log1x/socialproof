<?php

namespace SocialProof\Providers;

use SocialProof\Interfaces\ProviderInterface,
    SocialProof\Exceptions\ProviderException,
    SocialProof\Utilities\Utilities;

use GuzzleHttp\Client,
    GuzzleHttp\Psr7\Request,
    GuzzleHttp\Exception\RequestException,
    Psr\Http\Message\ResponseInterface;

class Linkedin implements ProviderInterface
{
    /**
     * Default API for this provider.
     *
     * @var string
     */
    protected $api = 'https://api.linkedin.com';

    /**
     * Default endpoint for this provider.
     *
     * @var string
     */
    protected $endpoint = '/v1/companies/%s/num-followers?format=json';

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
     * LinkedIn
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

        $linkedin = $this->client($config)->getAsync(sprintf($config->endpoint, $credentials->username), [
            'headers' => [
                'Authorization' => 'Bearer ' . $config->token
            ]
        ])->then(
            function (ResponseInterface $response) use ($config) {
                $response = json_decode($response->getBody()->getContents());
                return !empty($response) ? intval($response) : $config->default;
            },

            function (RequestException $error) use ($config) {
                return $config->default ? $error->getMessage() : $config->default;
            }
        );

        return $linkdin->wait();
    }
}
