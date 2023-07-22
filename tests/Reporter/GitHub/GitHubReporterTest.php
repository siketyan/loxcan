<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Reporter\MarkdownBuilder;

class GitHubReporterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<MarkdownBuilder>
     */
    private ObjectProphecy $markdownBuilder;

    /**
     * @var ObjectProphecy<GitHubClient>
     */
    private ObjectProphecy $client;

    private GitHubReporter $reporter;

    protected function setUp(): void
    {
        $this->markdownBuilder = $this->prophesize(MarkdownBuilder::class);
        $this->client = $this->prophesize(GitHubClient::class);

        $this->reporter = new GitHubReporter(
            $this->markdownBuilder->reveal(),
            $this->client->reveal(),
        );

        putenv('LOXCAN_REPORTER_GITHUB=1');
        putenv('LOXCAN_REPORTER_GITHUB_OWNER=foo');
        putenv('LOXCAN_REPORTER_GITHUB_REPO=bar');
        putenv('LOXCAN_REPORTER_GITHUB_ISSUE_NUMBER=123');
        putenv('LOXCAN_REPORTER_GITHUB_USERNAME=me');
    }

    public function test(): void
    {
        $filename = 'foo.lock';
        $markdown = '## Markdown';
        $diff = $this->prophesize(DependencyCollectionDiff::class)->reveal();
        $diffs = [$filename => $diff];

        $this->markdownBuilder->build($diffs)->willReturn($markdown);

        $this->client->getComments('foo', 'bar', 123)->willReturn([])->shouldBeCalledOnce();
        $this->client->createComment('foo', 'bar', 123, $markdown)->shouldBeCalledOnce();

        $this->reporter->report($diffs);
    }

    public function testUpdate(): void
    {
        $filename = 'foo.lock';
        $markdown = '## Markdown';
        $diff = $this->prophesize(DependencyCollectionDiff::class)->reveal();
        $diffs = [$filename => $diff];

        $me = $this->prophesize(GitHubUser::class);
        $me->getLogin()->willReturn('me');

        $comment = $this->prophesize(GitHubComment::class);
        $comment->getId()->willReturn(123);
        $comment->getAuthor()->willReturn($me->reveal());

        $this->markdownBuilder->build($diffs)->willReturn($markdown);

        $this->client->getComments('foo', 'bar', 123)->willReturn([$comment->reveal()])->shouldBeCalledOnce();
        $this->client->updateComment('foo', 'bar', $comment->reveal(), $markdown)->shouldBeCalledOnce();

        $this->reporter->report($diffs);
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->reporter->supports());

        putenv('LOXCAN_REPORTER_GITHUB=');

        $this->assertFalse($this->reporter->supports());
    }
}
