<?php

namespace SocialProof\Providers;

use SocialProof\Interfaces\ProviderInterface,
    SocialProof\Exceptions\ProviderException,
    SocialProof\Utilities\Utilities;

use GuzzleHttp\Client,
    GuzzleHttp\HandlerStack,
    GuzzleHttp\Psr7\Request,
    GuzzleHttp\Subscriber\Oauth\Oauth1,
    GuzzleHttp\Exception\RequestException,
    Psr\Http\Message\ResponseInterface;

class Twitter implements ProviderInterface
{
    /**
     * Default API for this provider.
     *
     * @var string
     */
    protected $api = 'https://api.twitter.com';

    /**
     * Default endpoint for this provider.
     *
     * @var string
     */
    protected $endpoint = '/1.1/users/show.json';

    /**
     * Default auth for this provider.
     *
     * @var string
     */
    protected $auth = 'oauth';

    /**
     * Credentials required for this provider.
     *
     * @var array
     */
    protected $credentials = [
        'username',
        'consumer_key',
        'consumer_secret',
        'token',
        'token_secret'
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
     * Twitter
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

        $config->middleware = new Oauth1([
            'consumer_key'    => $credentials->consumer_key,
            'consumer_secret' => $credentials->consumer_secret,
            'token'           => $credentials->token,
            'token_secret'    => $credentials->token_secret
        ]);

        $config->api = $config->api ?? $this->api;
        $config->endpoint = $config->endpoint ?? $this->endpoint;
        $config->auth = $this->auth;
        $config->stack = HandlerStack::create();
        $config->stack->push($config->middleware);

        $twitter = $this->client($config)->getAsync($config->endpoint, [
            'query'   => ['screen_name' => $credentials->username],
            'handler' => $config->stack,
            'auth'    => $config->auth
        ])->then(
          function (ResponseInterface $response) use ($config) {
              $response = json_decode($response->getBody()->getContents());
              return !empty($response->followers_count) ? intval($response->followers_count) : $config->default;
          },

          function (RequestException $error) use ($config) {
            return $config->debug ? $error->getMessage() : $config->default;
          }
        );

        return $twitter->wait();
    }
}
