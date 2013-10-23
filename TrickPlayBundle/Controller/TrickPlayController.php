<?php

namespace Talis\TrickPlayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Talis\SwiftForumBundle\Model\User;
use Talis\TrickPlayBundle\Entity\LodestoneCharacter;

/**
 * Description
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class TrickPlayController extends Controller
{
    /**
     * @Route("/roster", name="basic_roster")
     */
    public function rosterAction()
    {
        $GATHERING_PROFESSIONS = array("fisher", "miner", "botanist");

        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository('TalisTrickPlayBundle:LodestoneFreeCompany')->get();
        $permissions = $this->get('security.role_hierarchy')->getMap();
        $characters = $company->getMembers()->toArray();
        $gatheringProfessionCount = array();
        $craftingProfessionCount = array();
        $jobCount = array();

        // Append metadata to characters
        $characters = array_map(function($character) use($permissions, $GATHERING_PROFESSIONS, &$jobCount, &$craftingProfessionCount, &$gatheringProfessionCount) {
            $meta = array("character" => $character);
            $user = $character->getUser();

            // Keep a survey of profession numbers
            foreach ($character->getProfessions() as $profession => $level) {
                if (in_array($profession, $GATHERING_PROFESSIONS)) {
                    if (!isset($gatheringProfessionCount[$profession])) $gatheringProfessionCount[$profession] = 0;
                    $gatheringProfessionCount[$profession] ++;
                } else {
                    if (!isset($craftingProfessionCount[$profession])) $craftingProfessionCount[$profession] = 0;
                    $craftingProfessionCount[$profession] ++;
                }
            }

            // Keep a survey of job numbers
            foreach ($character->getJobs() as $job => $level) {
                if (!isset($jobCount[$job])) $jobCount[$job] = 0;
                $jobCount[$job] ++;
            }

            if ($user) {
                $permissions = ($user->getRole() && isset($permissions[$user->getRole()->getRole()])) ? $permissions[$user->getRole()->getRole()] : array();

                // Rank is only shown if above member
                $meta["user"] = $user;
                $meta["rank"] = in_array("ROLE_MEMBER", $permissions) ? $user->getRole()->getName() : null;
                $meta["power"] = count($permissions);
            } else {
                $meta["user"] = null;
                $meta["rank"] = "Unregistered";
                $meta["power"] = -1;
            }

            return $meta;
        }, $characters);

        // Sort class counts
        ksort($gatheringProfessionCount);
        ksort($craftingProfessionCount);
        ksort($jobCount);

        // Sort characters by permission level, and then by name
        usort($characters, function($first, $second) use($permissions) {
            return ($first["power"] == $second["power"]) ? strcmp($first["character"]->getName(), $second["character"]->getName()) : ($second["power"] - $first["power"]);
        });

        // Render page
        return $this->render('TalisTrickPlayBundle:TrickPlay:roster.html.twig', array(
            "members" => $characters,
            "gathering" => $gatheringProfessionCount,
            "crafting" => $craftingProfessionCount,
            "jobs" => $jobCount
        ));
    }
}
