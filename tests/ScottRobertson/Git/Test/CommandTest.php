<?php

namespace ScottRobertson\Git\Test;

class CommandTest extends TestCase
{
    public function testGetAndSetPath()
    {
        $path = __DIR__;

        $command = new \ScottRobertson\Git\Command(
            $path
        );

        $this->assertEquals($path, $command->getPath());
    }

    public function testExecuteAndGetters()
    {
        $path = __DIR__;

        $command = new \ScottRobertson\Git\Command($path);
        $command->execute('status');

        $this->assertInternalType('array', $command->getOutput());
        $this->assertEquals(0, $command->getResponse());
        $this->assertEquals('git status', $command->getCommand());
    }

    public function testExecuteFail()
    {
        $this->setExpectedException('ScottRobertson\Git\GitException');
        $path = __DIR__;

        $command = new \ScottRobertson\Git\Command($path);
        $command->execute('commanddoesnotexists');
    }
}

