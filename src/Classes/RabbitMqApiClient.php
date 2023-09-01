<?php

declare(strict_types=1);

namespace Pavelmgn\GptPulseConnector\Classes;

use GuzzleHttp\Client;

class RabbitMqApiClient
{
    private string $user;

    private string $pass;

    private Client $client;

    private string $url;

    public function __construct(string $user, string $pass, string $url)
    {
        $this->user = $user;
        $this->pass = $pass;
        $this->client = new Client([
            'auth' => [$user, $pass],
        ]);
        $this->url = $url;
    }

    public function createVhost(string $name)
    {
        $this->client->put($this->url . '/api/vhosts/' . $name, [
            'headers' => ['content-type' => 'application/json']
        ]);
    }

    public function createUser(string $name, string $pass)
    {
        $this->client->put($this->url . '/api/users/' . $name, [
            'headers' => ['content-type' => 'application/json'],
            'body'    => '{"password":"' . $pass . '", "tags": "management"}',
        ]);
    }

    public function setPermission(string $vhost, string $user)
    {
        $this->client->put($this->url . '/api/permissions/' . $vhost . '/' . $user, [
            'headers' => ['content-type' => 'application/json'],
            'body'    => '{"configure":".*", "write": ".*", "read": ".*"}',
        ]);
    }
}
