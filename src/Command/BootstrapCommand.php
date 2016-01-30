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
        $output->writeln('<comment>Collect required bootstrap components</comment>');
        $bootstrap = $this->getJsonContent(__DIR__.'/../Resources/assets/bootstrap.json');

        foreach ($this->getApplication()->getGitiki()->getExtensions() as $extension) {
            if (!$extension instanceof BootstrapInterface) {
                continue;
            }

            $bootstrap = array_replace_recursive($bootstrap, $this->getJsonContent($extension->getBootstrap()));
        }

        $destinationPath = __DIR__.'/../../webpack/bootstrap.json';
        $output->writeln('<comment>Write compiled bootstrap components</comment>');
        file_put_contents($destinationPath, json_encode($bootstrap, JSON_PRETTY_PRINT));

        $output->writeln(sprintf(
            '<info>The bootstrap.json has been successfully written</info>: %s</info>',
            realpath($destinationPath)
        ));
    }

    private function getJsonContent($jsonPath)
    {
        return json_decode(file_get_contents($jsonPath), true);
    }
}
