<?php

namespace DiffReviewer\DiffReviewer\Git;

use Github\AuthMethod;
use Github\Client;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\HttpClient\HttplugClient;


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
        $client = Client::createWithHttpClient(new HttplugClient());
        $client->authenticate('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', null, AuthMethod::ACCESS_TOKEN);

        return $client;
    }
}
