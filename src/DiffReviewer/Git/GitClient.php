<?php

namespace DiffReviewer\DiffReviewer\Git;

use Github\Client;
use GuzzleHttp\Client as GuzzleClient;


class GitClient
{
    protected $client;
    public function __construct()
    {
        $this->client = $this->createClient();
    }

    public function getPrDiff()
    {
        /**
         * @var \Github\Api\PullRequest $pr
         */
        $pr = $this->client->api('pr');
        $pr = $pr->configure('diff');
        return $pr->show('spryker', 'spryker', 10768);
    }

    protected function createClient()
    {
        $client = Client::createWithHttpClient(new GuzzleClient());
        $client->authenticate('PUT_TOKEN_HERE', null, Client::AUTH_ACCESS_TOKEN);

        return $client;
    }
}
