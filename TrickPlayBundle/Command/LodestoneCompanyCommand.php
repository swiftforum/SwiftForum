<?php

namespace Talis\TrickPlayBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LodestoneCompanyCommand extends ContainerAwareCommand
{
    const NAME = "TrickPlay";

    protected function configure()
    {
        $this->setName('lodestone:company')->setDescription('Updates company data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get("Doctrine")->getManager();
        $company = $em->getRepository('TalisTrickPlayBundle:LodestoneFreeCompany')->get();
        $lodestone = $this->getContainer()->get("Lodestone");

        // Search for company if not yet set
        if (!$company->getId()) {
          $output->writeln("Searching for " . self::NAME . "...");
          $companies = $lodestone->searchFreeCompanies(self::NAME);

          if (empty($companies)) {
            $output->writeln("Error: Searching for " . self::NAME . " yielded nothing.");
            return;
          }

          $company->setId($companies[0]["id"]);
        }

        // Update company data
        $output->writeln("Getting company data...");
        $lodestoneData = $lodestone->getFreeCompany($company->getId());
        if (!$lodestoneData) {
            $output->writeln("Error: Cannot get company data.");
            return;
        }

        // Save company
        $output->writeln("Saving company...");
        $company->set($lodestoneData);
        $em->persist($company);
        $em->flush();

        $memberIds = array_map(function($member) { return $member["id"]; }, $lodestoneData["members"]);

        // Strip free company association from non-member characters
        $query = "UPDATE TalisTrickPlayBundle:LodestoneCharacter SET freeCompany = null WHERE id NOT IN :ids";
        $em->createQuery($query)->setParameter("ids", $memberIds);

        // Create/update members
        $output->writeln("Saving members...");
        $memberRepository = $em->getRepository('TalisTrickPlayBundle:LodestoneCharacter');
        foreach ($lodestoneData["members"] as $member) {
          $character = $memberRepository->get($member["id"]);
          $character->setFreeCompany($company);
          $character->set($member, false);
          $em->persist($character);
        }

        $em->flush();

        $output->writeln("Success");
    }
}
