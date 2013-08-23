<?php

namespace Talis\TrickPlayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Talis\SwiftForumBundle\Model\User;

/**
 * Description
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class TrickPlayController extends Controller
{
    /**
     * @Route("/guides", name="ffxiv_guides")
     */
    public function guideAction()
    {
        return $this->render('TalisTrickPlayBundle:TrickPlay:guides.html.twig');
    }

    /**
     * @Route("/roster", name="basic_roster")
     */
    public function rosterAction()
    {
        $currentUserRole = $this->get('security.context')->getToken()->getUser()->getRole()->getRole();
        $role_map = $this->get('security.role_hierarchy')->getMap();
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('TalisTrickPlayBundle:User')->findAll();
        $userMembers = array();

        // Can only edit roster if higher than Member
        $rosterEditable = in_array("ROLE_MEMBER", $role_map[$currentUserRole]);

        // Can only select ranks that are below
        $availableRanks = array(
            "ROLE_ADMIN" => in_array("ROLE_ADMIN", $role_map[$currentUserRole]) ? $em->getRepository('TalisTrickPlayBundle:Role')->getByIdentifier("ROLE_ADMIN")->getName() : null,
            "ROLE_OFFICER" => in_array("ROLE_OFFICER", $role_map[$currentUserRole]) ? $em->getRepository('TalisTrickPlayBundle:Role')->getByIdentifier("ROLE_OFFICER")->getName() : null,
            "ROLE_MEMBER" => in_array("ROLE_MEMBER", $role_map[$currentUserRole]) ? $em->getRepository('TalisTrickPlayBundle:Role')->getByIdentifier("ROLE_MEMBER")->getName() : null,
            "ROLE_GUEST" => in_array("ROLE_GUEST", $role_map[$currentUserRole]) ? $em->getRepository('TalisTrickPlayBundle:Role')->getByIdentifier("ROLE_GUEST")->getName() : null,
            "ROLE_BANNED" => in_array("ROLE_BANNED", $role_map[$currentUserRole]) ? $em->getRepository('TalisTrickPlayBundle:Role')->getByIdentifier("ROLE_BANNED")->getName() : null
        );

        /** @var User $user */
        foreach($users as $user) {

            // Only display member and higher
            if ( isset($role_map[$user->getRole()->getRole()]) &&
                (   $user->getRole()->getRole() === 'ROLE_MEMBER' ||
                    in_array('ROLE_MEMBER', $role_map[$user->getRole()->getRole()])
                ) ) {

                $userRole = $user->getRole()->getRole();
                $character = $em->getRepository('TalisTrickPlayBundle:Character')
                    ->findOneBy(array('user' => $user, 'isPrimary' => true));

                // Can only edit users whose role is under the current user's
                $editable = in_array($userRole, $role_map[$currentUserRole]);

                if($character) {
                    $userMembers[count($role_map[$userRole])][] = array('user' => $user, "editable" => $editable, 'character' => $character);
                } else {
                    $userMembers[count($role_map[$userRole])][] = array('user' => $user, "editable" => $editable);
                }

                unset($character);
            }
        }

        ksort($userMembers);
        $userMembers = array_reverse($userMembers);

        return $this->render('TalisTrickPlayBundle:TrickPlay:roster.html.twig', array(
            'users' => $userMembers,
            "rosterEditable" => $rosterEditable,
            "availableRanks" => $availableRanks
        ));
    }
}
