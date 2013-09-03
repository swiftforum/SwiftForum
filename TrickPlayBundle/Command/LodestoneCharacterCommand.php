<?php

namespace Talis\TrickPlayBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LodestoneCharacterCommand extends Command
{
    protected function configure()
    {
        $this->setName('lodestone:character')->setDescription('Updates a character data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // TODO
        $output->writeln("HELLO WORLD");
    }
}
