<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Git;

use Eloquent\Pathogen\PathInterface;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\Repository;
use Symfony\Component\Process\Process;

class GitProcessFactoryTest extends TestCase
{
    private const GIT_PATH = '/usr/bin/git';

    private GitProcessFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new GitProcessFactory(
            self::GIT_PATH,
        );
    }

    public function test(): void
    {
        $path = $this->createStub(PathInterface::class);
        $repository = $this->createStub(Repository::class);

        $path->method('string')->willReturn(__DIR__);
        $repository->method('getPath')->willReturn($path);

        $process = $this->factory->create($repository, ['foo', 'bar']);

        $this->assertInstanceOf(Process::class, $process);
        $this->assertSame("'/usr/bin/git' 'foo' 'bar'", $process->getCommandLine());
        $this->assertSame(__DIR__, $process->getWorkingDirectory());
    }
}
