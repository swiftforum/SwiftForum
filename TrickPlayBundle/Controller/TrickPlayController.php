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
        $role_map = $this->get('security.role_hierarchy')->getMap();
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('TalisTrickPlayBundle:User')
            ->findAll();

        $userMembers = array();

        /** @var User $user */
        foreach($users as $user) {
            if ( isset($role_map[$user->getRole()->getRole()]) &&
                (   $user->getRole()->getRole() === 'ROLE_MEMBER' ||
                    in_array('ROLE_MEMBER', $role_map[$user->getRole()->getRole()])
                ) ) {


                $character = $em->getRepository('TalisTrickPlayBundle:Character')
                    ->findOneBy(array('user' => $user, 'isPrimary' => true));

                if($character) {
                    $userMembers[count($role_map[$user->getRole()->getRole()])][] = array('user' => $user, 'character' => $character);
                } else {
                    $userMembers[count($role_map[$user->getRole()->getRole()])][] = array('user' => $user);
                }
                unset($character);
            }
        }

        ksort($userMembers);
        $userMembers = array_reverse($userMembers);

        return $this->render('TalisTrickPlayBundle:TrickPlay:roster.html.twig', array('users' => $userMembers, 'role_map' => $role_map ));
    }
}
