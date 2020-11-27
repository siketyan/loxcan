<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use Siketyan\Loxcan\Reporter\EnvironmentTrait;
use Siketyan\Loxcan\Reporter\ReporterInterface;

class GitHubReporter implements ReporterInterface
{
    use EnvironmentTrait;

    private GitHubMarkdownBuilder $markdownBuilder;
    private GitHubClient $client;

    public function __construct(
        GitHubMarkdownBuilder $markdownBuilder,
        GitHubClient $client
    ) {
        $this->markdownBuilder = $markdownBuilder;
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function report(array $diffs): void
    {
        $owner = $this->getEnv('LOXCAN_REPORTER_GITHUB_OWNER');
        $repo = $this->getEnv('LOXCAN_REPORTER_GITHUB_REPO');
        $issueNumber = (int) $this->getEnv('LOXCAN_REPORTER_GITHUB_ISSUE_NUMBER');
        $body = $this->markdownBuilder->build($diffs);

        $me = $this->client->getMe();
        $comments = $this->client->getComments($owner, $repo, $issueNumber);
        $myComments = array_filter($comments, fn (GitHubComment $comment): bool => $comment->getAuthor() === $me);

        if (count($myComments) > 0) {
            $this->client->updateComment(
                $owner,
                $repo,
                $myComments[array_key_first($myComments)],
                $body,
            );

            return;
        }

        $this->client->createComment($owner, $repo, $issueNumber, $body);
    }

    public function supports(): bool
    {
        $env = getenv('LOXCAN_REPORTER_GITHUB');

        return is_string($env) && $env !== '';
    }
}
