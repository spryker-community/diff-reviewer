<?php

namespace DiffReviewer\DiffReviewer\Git;

use Github\AuthMethod;
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
//        $client->authenticate('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', null, AuthMethod::ACCESS_TOKEN);
        $client->authenticate('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', null, AuthMethod::ACCESS_TOKEN);

        return $client;
    }
}
