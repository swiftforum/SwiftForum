<?php

namespace Talis\TrickPlayBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LodestoneCharacterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('lodestone:character')->setDescription('Updates a character data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get("Doctrine")->getManager();
        $lodestone = $this->getContainer()->get("Lodestone");

        $company = $em->getRepository('TalisTrickPlayBundle:LodestoneFreeCompany')->get();
        if (!$company) {
          $output->writeln("Error: Company data has not been loaded; please do that first");
          return;
        }

        // Find stale character
        $query = "SELECT c FROM TalisTrickPlayBundle:LodestoneCharacter c WHERE (c.freeCompany IS NOT NULL OR c.user IS NOT NULL) ORDER BY c.lastUpdated ASC";
        $characters = $em->createQuery($query)->setMaxResults(1)->getResult();

        if (empty($characters)) {
          $output->writeln("No characters to update");
          return;
        }

        // Get character data
        $character = $characters[0];
        $output->writeln("Getting character data for " . $character->getName() . "...");
        $lodestoneData = $lodestone->getCharacter($character->getId());
        if (!$lodestoneData) {
          $output->writeln("Error: Cannot get character data.");
          return;
        }

        $character->set($lodestoneData);

        // Save character
        $output->writeln("Saving character...");
        $em->persist($character);
        $em->flush();

        $output->writeln("Success");
    }
}
