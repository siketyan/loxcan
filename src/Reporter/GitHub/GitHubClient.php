<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Siketyan\Loxcan\Reporter\EnvironmentTrait;

class GitHubClient
{
    use EnvironmentTrait;

    private ClientInterface $httpClient;
    private GitHubUserPool $userPool;

    public function __construct(
        ClientInterface $httpClient,
        GitHubUserPool $userPool
    ) {
        $this->httpClient = $httpClient;
        $this->userPool = $userPool;
    }

    public function getMe(): GitHubUser
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                '/user',
                ['headers' => $this->getDefaultHeaders()],
            );
        } catch (GuzzleException $e) {
            throw new GitHubException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }

        $json = $response->getBody()->getContents();
        $assoc = json_decode($json, true);

        return $this->getOrCreateUser($assoc);
    }

    /**
     * @param string $owner
     * @param string $repo
     * @param int    $issueNumber
     *
     * @return GitHubComment[]
     */
    public function getComments(string $owner, string $repo, int $issueNumber): array
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                sprintf('/repos/%s/%s/issues/%d/comments', $owner, $repo, $issueNumber),
                ['headers' => $this->getDefaultHeaders()],
            );
        } catch (GuzzleException $e) {
            throw new GitHubException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }

        $json = $response->getBody()->getContents();
        $assoc = json_decode($json, true);
        $comments = [];

        foreach ($assoc as $row) {
            $comments[] = new GitHubComment(
                $row['id'],
                $row['body'],
                $this->getOrCreateUser($row['user']),
            );
        }

        return $comments;
    }

    public function createComment(string $owner, string $repo, int $issueNumber, string $body): void
    {
        try {
            $this->httpClient->request(
                'POST',
                sprintf('/repos/%s/%s/issues/%d/comments', $owner, $repo, $issueNumber),
                [
                    'headers' => $this->getDefaultHeaders(),
                    'body' => json_encode([
                        'body' => $body,
                    ]),
                ],
            );
        } catch (GuzzleException $e) {
            throw new GitHubException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
    }

    private function getDefaultHeaders(): array
    {
        return [
            'Accept' => 'application/vnd.github.v3+json',
            'Authorization' => sprintf(
                'token %s',
                $this->getEnv('LOXCAN_REPORTER_GITHUB_TOKEN'),
            ),
        ];
    }

    private function getOrCreateUser(array $assoc): GitHubUser
    {
        $id = $assoc['id'];
        $user = $this->userPool->get($id);

        if ($user === null) {
            $user = new GitHubUser($id, $assoc['login']);
            $this->userPool->add($user);
        }

        return $user;
    }
}
