<?php

namespace ScottRobertson\Git;

class Repository
{
    /**
     * Holds the Git Command object
     * @var ScottRobertson\Git\Command
     */
    private $command;

    /**
     * Holds the remote for this repo
     * @var string
     */
    private $remote;

    /**
     * Inject the Git Command object, this allows for testing
     * @author Scott Robertson <scottymeuk@gmail.com>
     * @param  ScottRobertson\Git\Command $command
     * @param  string $remote
     */
    public function __construct(
        \ScottRobertson\Git\Command $command,
        $remote
    )
    {
        $this->remote = $remote;
        $this->command = $command;

        if (! $this->exists()) {
            $this->getRepo();
        }
    }

    /**
     * Does this repo already exist?
     * @author Scott Robertson <scottymeuk@gmail.com>
     * @return bool
     */
    public function exists()
    {
        return is_dir($this->command->getPath() . '/.git');
    }

    /**
     * Clone the repo to the current path
     * @author Scott Robertson <scottymeuk@gmail.com>
     * @return ScottRobertson\Git\Command
     */
    public function getRepo()
    {
        return $this->command->execute(
            sprintf(
                'clone %s .',
                $this->remote
            )
        );
    }

    /**
     * Get the commits for this branch, with a skip and limit
     * @author Scott Robertson <scottymeuk@gmail.com>
     * @param  string  $branch
     * @param  integer $limit
     * @param  integer $skip
     * @return array
     */
    public function getCommits($branch = 'master', $limit = 10, $skip = 0)
    {
        $log = $this->command->execute(
            sprintf(
                'log %s --skip=%d -n %d',
                $branch,
                $skip,
                $limit
            )
        );

        $lineCount = 0;
        $commits = [];

        $logOutput = $log->getOutput();
        $logCount = count($logOutput);

        foreach($logOutput as $line){

            $lineCount++;

            if ($lineCount === $logCount || strpos($line, 'commit') === 0) {

                if (strpos($line, 'commit') !== 0) {
                    $commit['message'] .= trim($line);
                }

                if (! empty($commit)){
                    array_push($commits, $commit);
                    unset($commit);
                }

                $commit['hash'] = trim(substr($line, strlen('commit')));
            } elseif (strpos($line, 'Author') === 0) {
                $commit['author'] = trim(substr($line, strlen('Author:')));
            } elseif (strpos($line, 'Date') ===0) {
                $commit['date'] = trim(substr($line, strlen('Date:')));
            } else {
                $commit['message'] .= trim($line);
            }
        }

        return $commits;
    }

    /**
     * Run a fetch on the git repo
     * @author Scott Robertson <scottymeuk@gmail.com>
     * @param  boolean $all Should we fetch everything?
     * @return \ScottRobertson\Git\Command
     */
    public function fetch($all = true)
    {
        return $this->command->execute(
            sprintf(
                'fetch%s -p',
                ($all ? ' --all --tags' : null)
            )
        );
    }

    /**
     * Get a list of branches
     * @author Scott Robertson <scottymeuk@gmail.com>
     * @return array
     */
    public function getBranches()
    {
        $command = $this->command->execute('branch');

        return array_filter(
            preg_replace(
                '/[\s\*]/',
                '',
                $command->getOutput()
            )
        );
    }
}
