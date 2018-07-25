<?php

namespace SocialProof\Providers;

use SocialProof\Interfaces\ProviderInterface,
    SocialProof\Exceptions\ProviderException,
    SocialProof\Utilities\Utilities;

use GuzzleHttp\Client,
    GuzzleHttp\Psr7\Request,
    GuzzleHttp\Exception\RequestException,
    Psr\Http\Message\ResponseInterface;

class Pinterest implements ProviderInterface
{
    /**
     * Default API for this provider.
     *
     * @var string
     */
    protected $api = 'https://www.pinterest.com';

    /**
     * Default endpoint for this provider.
     *
     * @var string
     */
    protected $endpoint = '/';

    /**
     * Credentials required for this provider.
     *
     * @var array
     */
    protected $credentials = [
        'username'
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
     * Pinterest
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

        $pinterest = $this->client($config)->getAsync($config->endpoint . $credentials->username, [
        ])->then(
            function (ResponseInterface $response) use ($config) {
                $response = $response->getBody()->getContents();
                $regex = '/property\=\"pinterestapp:followers\" name\=\"pinterestapp:followers\" content\=\"(.*?)" data-app/';
                preg_match($regex, $response, $matches);
                return !empty($matches[1]) && intval($matches[1]) > 0 ? intval($matches[1]) : $config->default;
            },

            function (RequestException $error) use ($config) {
                return $config->debug ? $error->getMessage() : $config->default;
            }
        );

        return $pinterest->wait();
    }
}
