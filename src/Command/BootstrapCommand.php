<?php

namespace Gitiki\Command;

use Gitiki\Extension\BootstrapInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BootstrapCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('bootstrap')
            ->setDescription('Generate the bootstrap.json')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command generate the bootstrap.json:
  <info>php %command.full_name%</info>
  <info>php %command.full_name% --wiki-dir="wiki/dir/"</info>
EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bootstrap = $this->getJsonContent(__DIR__.'/../Resources/assets/bootstrap.json');

        foreach ($this->getApplication()->getGitiki()->getExtensions() as $extension) {
            if (!$extension instanceof BootstrapInterface) {
                continue;
            }

            $bootstrap = array_replace_recursive($bootstrap, $this->getJsonContent($extension->getBootstrap()));
        }

        file_put_contents(__DIR__.'/../../webpack/bootstrap.json', json_encode($bootstrap, JSON_PRETTY_PRINT));
    }

    private function getJsonContent($jsonPath)
    {
        return json_decode(file_get_contents($jsonPath), true);
    }
}
