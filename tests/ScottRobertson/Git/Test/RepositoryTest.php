<?php

namespace ScottRobertson\Git\Test;

class RepositoryTest extends TestCase
{
    public function testConstruct()
    {
        $remote = 'git@github.com:scottrobertson/php-git.git';
        $path = __DIR__;

        $command = \Mockery::mock('\ScottRobertson\Git\Command')
            ->shouldReceive('getPath')
            ->once()
            ->andReturn($path)
            ->shouldReceive('execute')
            ->once()
            ->with('clone ' . $remote . ' .')
            ->mock();

        $repository = new \ScottRobertson\Git\Repository(
            $command,
            $remote
        );
    }

    public function testGetCommits()
    {
        $remote = 'git@github.com:scottrobertson/php-git.git';
        $path = __DIR__;

        $branch = 'master';
        $skip = 1;
        $limit = 10;

        $commandOutput = [
            'commit 6aa799ad94dd24eafa997db7a4c2de28b43d4fcb',
            'Author: Scott Robertson <scottrobertson@users.noreply.github.com>',
            'Date:   Wed Sep 25 15:39:32 2013 +0100',
            'Update README.md',
            'commit fb7008893e1314aaadc5927ef85b133dc1d512b8',
            'Author: Scott Robertson <scottrobertson@users.noreply.github.com>',
            'Date:   Wed Sep 25 15:42:31 2013 +0100',
            'Update LICENCE'
        ];

        $command = \Mockery::mock('\ScottRobertson\Git\Command')
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn($commandOutput)
            ->mock();

        $command
            ->shouldReceive('getPath')
            ->once()
            ->andReturn($path)
            ->shouldReceive('execute')
            ->once()
            ->with('clone ' . $remote . ' .')
            ->shouldReceive('execute')
            ->once()
            ->with(
                sprintf(
                    'log %s --skip=%d -n %d',
                    $branch,
                    $skip,
                    $limit
                )
            )
            ->andReturn($command)
            ->mock();

        $repository = new \ScottRobertson\Git\Repository(
            $command,
            $remote
        );

        $commits = $repository->getCommits($branch, $limit, $skip);

        $expected = [
            [
                'hash' => '6aa799ad94dd24eafa997db7a4c2de28b43d4fcb',
                'author' => 'Scott Robertson <scottrobertson@users.noreply.github.com>',
                'date' => 'Wed Sep 25 15:39:32 2013 +0100',
                'message' => 'Update README.md',
            ],
            [
                'hash' => 'fb7008893e1314aaadc5927ef85b133dc1d512b8',
                'author' => 'Scott Robertson <scottrobertson@users.noreply.github.com>',
                'date' => 'Wed Sep 25 15:42:31 2013 +0100',
                'message' => 'Update LICENCE',
            ]
        ];

        $this->assertEquals($expected, $commits);
    }

    public function testFetch()
    {
        $remote = 'git@github.com:scottrobertson/php-git.git';
        $path = __DIR__;

        $command = \Mockery::mock('\ScottRobertson\Git\Command')
            ->shouldReceive('getPath')
            ->once()
            ->andReturn($path)
            ->shouldReceive('execute')
            ->once()
            ->with('clone ' . $remote . ' .')
            ->shouldReceive('execute')
            ->once()
            ->with('fetch --all --tags -p')
            ->shouldReceive('execute')
            ->once()
            ->with('fetch -p')
            ->mock();

        $repository = new \ScottRobertson\Git\Repository(
            $command,
            $remote
        );

        $repository->fetch();
        $repository->fetch(false);
    }

    public function testGetBranches()
    {
        $remote = 'git@github.com:scottrobertson/php-git.git';
        $path = __DIR__;

        $commandOutput = [
            '* master',
            'develop',
            'feature/awesome'
        ];

        $command = \Mockery::mock('\ScottRobertson\Git\Command')
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn($commandOutput)
            ->mock();

        $command
            ->shouldReceive('getPath')
            ->once()
            ->andReturn($path)
            ->shouldReceive('execute')
            ->once()
            ->with('clone ' . $remote . ' .')
            ->shouldReceive('execute')
            ->once()
            ->with('branch')
            ->andReturn($command)
            ->mock();

        $repository = new \ScottRobertson\Git\Repository(
            $command,
            $remote
        );

        $branches = $repository->getBranches();

        $expected = [
            'master',
            'develop',
            'feature/awesome'
        ];

        $this->assertEquals($expected, $branches);
    }
}

