<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\SecurityBundle\Security;

class JiraService
{
    private Client $client;


    public function __construct(private readonly Security $security)
    {
        $this->client = new Client([
            'base_uri' => getenv('BASE_URI'),
            'auth' => ['n21601201@gmail.com', getenv('AUTH_TOKEN')],
        ]);


    }

    public function getUserIssues($user): array
    {
        $response = $this->client->get("search?jql=reporter=".$this->getUserByEmail($user));
        $response = json_decode($response->getBody()->getContents(), true);
        return $response['issues'];
    }

    /**
     * @throws GuzzleException
     */
    public function getUserByEmail(string $email)
    {
        $response = $this->client->get('user/search?query='.$email);
        $data = json_decode($response->getBody(), true);
        if($data)
        {
            return $data[0]['accountId'];
        }
        return null;
    }

    public function generateLink($key)
    {
        return 'https://bondarkov.atlassian.net/browse/'.$key;
    }

    public function createTicket(array $request, string $url, ?int $collection, string $user) {

        $response = $this->client->post('issue', [
            'json' => [
                'fields' => [
                    'project' => [
                        'key' => 'KAN'
                    ],
                    'summary' => $request['name'],
                    'description' => $request['description'],
                    'issuetype' => [
                        'name' => 'Task'
                    ],
                    'reporter' => [
                        'accountId' => $user,
                    ],
                    'priority' => [
                        'id' => $request['priority'],
                    ],
                    'customfield_10034' => $url ,
                    'customfield_10035' => $collection,
                    ]
                ]
            ]);

        return $response->getBody();
    }

    public function getJiraKey($response)
    {
        $response = json_decode($response->getContents(), true);
        return $response['key'];
    }

    public function createUser() {

        $user = $this->security->getUser();
        $response = $this->client->post('user', [
            'json' => [
                'emailAddress' => $user->getEmail(),
                'products' => ['jira-software']
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        return $data['accountId'];
    }
}