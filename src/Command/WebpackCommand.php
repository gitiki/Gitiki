<?php

namespace Gitiki\Command;

use Gitiki\Extension\WebpackInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebpackCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('webpack')
            ->setDescription('Generate webpack entries')
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
        $output->writeln('<comment>Collect webpack entries</comment>');

        foreach ($this->getApplication()->getGitiki()->getExtensions() as $extension) {
            if (!$extension instanceof WebpackInterface) {
                continue;
            }

            foreach ($extension->getWebpackEntries() as $name => $requires) {
                $output->writeln(sprintf('<info>Write %s.entry.js file</info>', $name));
                $f = fopen(sprintf('%s/../../webpack/%s.entry.js', __DIR__, $name) , 'w');

                foreach ((array) $requires as $require) {
                    fwrite($f, sprintf('require("%s");', $require)."\n");
                }

                fclose($f);
            }
        }
    }
}
