<?php

namespace Gitiki;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Console extends Application
{
    private $gitiki;

    public function __construct()
    {
        parent::__construct('Gitiki', Gitiki::VERSION);
    }

    public function getGitiki()
    {
        return $this->gitiki;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $wikiDir = $this->getWikiDir($input) ?: getcwd();
        $this->gitiki = new Gitiki($wikiDir);

        return parent::doRun($input, $output);
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $commands[] = new Command\BootstrapCommand();
        $commands[] = new Command\WebpackCommand();

        return $commands;
    }

    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();

        $definition->addOption(new InputOption('--wiki-dir', '-d', InputOption::VALUE_REQUIRED, 'If specified, use the given directory as wiki directory.'));

        return $definition;
    }

    private function getWikiDir(InputInterface $input)
    {
        $wikiDir = $input->getParameterOption(array('--wiki-dir', '-d'));

        if (false !== $wikiDir && !is_dir($wikiDir)) {
            throw new \RuntimeException('Invalid wiki directory specified.');
        }

        return $wikiDir;
    }
}
