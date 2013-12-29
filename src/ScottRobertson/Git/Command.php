<?php

namespace ScottRobertson\Git;

class Command
{
    /**
     * Holds the final command to run
     * @var string
     */
    private $command;

    /**
     * The output of the command
     * @var array
     */
    private $output;

    /**
     * The response code of the command
     * @var integer
     */
    private $response;

    /**
     * Holds the location of this git repo
     * @var string
     */
    private $path;

    /**
     * Setup the Git Command class
     * @author Scott Robertson <scottymeuk@gmail.com>
     * @param  string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
        if (! is_dir($this->getPath())) {
            mkdir($this->getPath(), 0777, true);
        }
    }

    /**
     * Returns the path to the repo
     * @author Scott Robertson <scottymeuk@gmail.com>
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Run the command
     * @author Scott Robertson <scottymeuk@gmail.com>
     * @param  string $command
     * @return \ScottRobertson\Git\Command
     */
    public function execute($command)
    {
        $currentPath = getcwd();
        chdir($this->getPath());

        $this->command = 'git ' . $command;

        ob_start();
        passthru($this->command . ' 2>&1', $this->response);
        $this->output = trim(ob_get_contents());
        ob_end_clean();

        if ($this->getResponse() !== 0) {
            throw new \ScottRobertson\Git\GitException(
                sprintf(
                    'Command "%s" could not be excuted. Output: "%s". Response Code: "%d"',
                    $this->getCommand(),
                    $this->output,
                    $this->getResponse()
                )
            );
        }

        chdir($currentPath);

        return $this;
    }

    /**
     * Returns the command that we ran
     * @author Scott Robertson <scottymeuk@gmail.com>
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Return the response code
     * @author Scott Robertson <scottymeuk@gmail.com>
     * @return integer
     */
    public function getResponse()
    {
        return (int) $this->response;
    }

    /**
     * Returns the output of the command as an array
     * @author Scott Robertson <scottymeuk@gmail.com>
     * @return array
     */
    public function getOutput()
    {
        // Explode the output on each line so we can parse it later
        // if we need to do so.
        if (! is_array($this->output)) {
            $this->output = explode("\n", $this->output);
        }

        return $this->output;
    }
}
