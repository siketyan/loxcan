<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use Siketyan\Loxcan\Model\DependencyCollectionDiff;
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

    public function report(DependencyCollectionDiff $diff, string $filename): void
    {
        $this->client->comment(
            $this->getEnv('LOXCAN_REPORTER_GITHUB_OWNER'),
            $this->getEnv('LOXCAN_REPORTER_GITHUB_REPO'),
            (int) $this->getEnv('LOXCAN_REPORTER_GITHUB_ISSUE_NUMBER'),
            $this->markdownBuilder->build($diff, $filename),
        );
    }

    public function supports(): bool
    {
        $env = getenv('LOXCAN_REPORTER_GITHUB');

        return is_string($env) && $env !== '';
    }
}
