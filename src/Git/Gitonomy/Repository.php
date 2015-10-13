<?php

namespace Gitiki\Git\Gitonomy;

use Gitonomy\Git\Repository as BaseRepository;

use Symfony\Component\Process\ProcessBuilder;

class Repository extends BaseRepository
{
    protected $perlCommand;

    private $wikiDir;

    public function __construct($dir, $options = [])
    {
        $options['command'] = $options['git_command'];
        $this->perlCommand = $options['perl_command'];
        $this->wikiDir = $options['wiki_dir'];
        unset($options['git_command'], $options['perl_command'], $options['wiki_dir']);

        parent::__construct($dir, $options);
    }

    public function getWikiDir()
    {
        return $this->wikiDir;
    }

    public function getFile($file, $revision)
    {
        return $this->run('show', [$revision.':'.$this->wikiDir.$file]);
    }

    public function getCommit($hash)
    {
        if (!isset($this->objects[$hash])) {
            $this->objects[$hash] = new Commit($this, $hash);
        }

        return $this->objects[$hash];
    }

    public function getLog($revisions = null, $paths = null, $offset = null, $limit = null)
    {
        if (null !== $paths) {
            if (is_string($paths)) {
                $paths = $this->wikiDir.$paths;
            } elseif (is_array($paths)) {
                foreach ($paths as $i => $path) {
                    $paths[$i] = $this->wikiDir.$paths;
                }
            }
        }

        return parent::getLog($revisions, $paths, $offset, $limit);
    }

    public function run($command, $args = [])
    {
        $output = parent::run($command, $args);

        if ('diff-tree' === $command && null !== $this->perlCommand) {
            $p = ProcessBuilder::create([$this->perlCommand, __DIR__.'/../Resources/bin/git-diff-highlight'])
                ->setInput($output)
                ->getProcess();

            $p->run();
            if ($p->isSuccessful()) {
                $output = $p->getOutput();
            }
        }

        return $output;
    }
}
