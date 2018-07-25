<?php

namespace SocialProof\Builders;

use SocialProof\Exceptions\BuilderException;

class ProviderBuilder
{
    /**
     * Provider Namespace
     *
     * @var string
     */
    protected $namespace = '\\SocialProof\\Providers\\';

    /**
     * Available Providers
     *
     * @var array
     */
    protected $providers = [
        'facebook',
        'twitter',
        'instagram',
        'pinterest',
        'linkedin'
    ];

    /**
     * The provider to use.
     *
     * @var \SocialProof\Interfaces\ProviderInterface
     */
    protected $provider;

    /**
     * Provider Credentials
     *
     * @var array
     */
    protected $credentials;

    /**
     * Additional Provider Configuration
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new Builder instance.
     *
     * @param array $credentials
     * @param array $config
     */
    public function __construct($credentials = [], $config = [])
    {
        $this->config = [
            'default' => 0,
            'debug'   => false,
            'timeout' => 60
        ];
        $this->credentials = [];

        $this->updateCredentials($credentials);
        $this->updateConfig($config);
    }

    /**
     * Set the username for the provider.
     *
     * @param  string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->setCredential('username', $username);

        return $this;
    }

    /**
     * Set the token for the provider.
     *
     * @param  string $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->setCredential('token', $token);

        return $this;
    }

    /**
     * Set the token secret for the provider.
     *
     * @param  string $secret
     * @return $this
     */
    public function setTokenSecret($secret)
    {
        $this->setCredential('token_secret', $secret);

        return $this;
    }

    /**
     * Set the consumer key for the provider.
     *
     * @param  string $key
     * @return $this
     */
    public function setConsumerKey($key)
    {
        $this->setCredential('consumer_key', $key);

        return $this;
    }

    /**
     * Set the consumer secret for the provider.
     *
     * @param  string $secret
     * @return $this
     */
    public function setConsumerSecret($secret)
    {
        $this->setCredential('consumer_secret', $secret);

        return $this;
    }

    /**
     * Set the API for the provider.
     *
     * @param  string $api
     * @return $this
     */
    public function setApi($api)
    {
        $this->setConfig('api', $api);

        return $this;
    }

    /**
     * Set the endpoint for the provider.
     *
     * @param  string $endpoint
     * @return $this
     */
    public function setEndpoint($endpoint)
    {
        $this->setConfig('endpoint', $endpoint);

        return $this;
    }

    /**
     * Set the timeout for the provider.
     *
     * @param  integer $timeout
     * @return $this
     */
    public function setTimeout(int $timeout)
    {
        $this->setConfig('timeout', $timeout);

        return $this;
    }

    /**
     * Set the default value for the provider.
     *
     * @param  mixed $default
     * @return $this
     */
    public function setDefault($default)
    {
        $this->setConfig('default', $default);

        return $this;
    }

    /**
     * Enable debug for the provider.
     *
     * @param  boolean $debug
     * @return $this
     */
    public function setDebug($debug = true)
    {
        $this->setConfig('debug', $debug);

        return $this;
    }

    /**
     * Get the current config as an object.
     *
     * @return object
     */
    private function getConfig()
    {
        return (object) $this->config;
    }

    /**
     * Set configs using an array.
     *
     * @param  array $configs
     * @return $this
     */
    public function setConfigs(array $configs)
    {
        $this->updateConfig($configs);

        return $this;
    }

    /**
     * Set a config key -> value pair.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return $this
     */
    public function setConfig($key, $value)
    {
        $this->updateConfig([$key => $value]);

        return $this;
    }

    /**
     * Update multiple config values using an array of key -> value pairs.
     *
     * @param  array $config
     * @return $this
     */
    public function updateConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);

        return $this;
    }

    /**
     * Get the current credentials as an object.
     *
     * @return object
     */
    private function getCredentials()
    {
        return (object) $this->credentials;
    }

    /**
     * Set credentials using an array.
     *
     * @param  array $credentials
     * @return $this
     */
    public function setCredentials(array $credentials)
    {
        $this->updateCredentials($credentials);

        return $this;
    }

    /**
     * Set a credential key -> value pair.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return $this
     */
    public function setCredential($key, $value)
    {
        $this->updateCredentials([$key => $value]);

        return $this;
    }

    /**
     * Update multiple credential values using an array of key -> value pairs and transform it into an object.
     *
     * @param  array $credentials
     * @return $this
     */
    public function updateCredentials(array $credentials)
    {
        $this->credentials = array_merge($this->credentials, $credentials);

        return $this;
    }

    /**
     * Pass the builder to the provider's get method.
     *
     * @return mixed
     */
    public function get()
    {
        if (empty($this->provider)) {
            throw new BuilderException(
                'Cannot get a null provider.'
            );
        }

        if (empty($this->credentials)) {
            throw new BuilderException(
                'Cannot get a provider without credentials.'
            );
        }

        return (new $this->provider())->get($this->getCredentials(), $this->getConfig());
    }

    /**
     * Set a provider if it exists.
     *
     * @param  string $name
     * @param  array  $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (!in_array($name, $this->providers)) {
            throw new BuilderException(
                "The method or provider {$name} does not exist."
            );
        }

        $this->provider = $this->namespace . ucwords($name);

        return $this;
    }
}
