<?php

namespace Talis\TrickPlayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * @Route("/users/{id}/role", name="change_role")
     * @Method({"POST"})
     */
    public function changeRoleAction($id)
    {
        $request = Request::createFromGlobals();
        $role = $request->request->get("role", null);
        $em = $this->getDoctrine()->getManager();

        $targetUser = $em->getRepository('TalisTrickPlayBundle:User')->findOneBy(array('id' => $id));
        if (!$targetUser) return new JsonResponse(array("error" => "User not found"), 404);

        $targetUserRole = $targetUser->getRole()->getRole();

        $currentUserRole = $this->get('security.context')->getToken()->getUser()->getRole()->getRole();
        $roleMap = $this->get('security.role_hierarchy')->getMap();

        // Can only update if current user is higher than Member
        $rosterEditable = in_array("ROLE_MEMBER", $roleMap[$currentUserRole]);
        if (!$rosterEditable) return new JsonResponse(array("error" => "Not allowed to edit"), 403);

        // Can only update if current user is higher than target user
        $userEditable = in_array($targetUserRole, $roleMap[$currentUserRole]);
        if (!$userEditable) return new JsonResponse(array("error" => "Not allowed to edit"), 403);

        // Update user's role
        $role = $em->getRepository('TalisTrickPlayBundle:Role')->getByIdentifier($role);
        if (!$role) return new JsonResponse(array("error" => "Invalid role identifier"), 400);

        $targetUser->setRole($role);
        $em->persist($targetUser);
        $em->flush();

        return new JsonResponse(array("success" => true));
    }

    /**
     * @Route("/roster", name="basic_roster")
     */
    public function rosterAction()
    {
        $em = $this->getDoctrine()->getManager();
        $role_map = $this->get('security.role_hierarchy')->getMap();

        $currentUser = $this->get('security.context')->getToken()->getUser();
        $currentUserRole = ($currentUser && gettype($currentUser) == "object" && $currentUser->getRole()) ? $currentUser->getRole()->getRole() : "ROLE_GUEST";
        $rolesUnder = isset($role_map[$currentUserRole]) ? $role_map[$currentUserRole] : array();

        $users = $em->getRepository('TalisTrickPlayBundle:User')->findAll();
        $userMembers = array();

        // Can only edit roster if higher than Member
        $rosterEditable = in_array("ROLE_MEMBER", $rolesUnder);

        // Can only select ranks that are below
        $availableRanks = array(
            "ROLE_ADMIN" => in_array("ROLE_ADMIN", $rolesUnder) ? $em->getRepository('TalisTrickPlayBundle:Role')->getByIdentifier("ROLE_ADMIN")->getName() : null,
            "ROLE_OFFICER" => in_array("ROLE_OFFICER", $rolesUnder) ? $em->getRepository('TalisTrickPlayBundle:Role')->getByIdentifier("ROLE_OFFICER")->getName() : null,
            "ROLE_MEMBER" => in_array("ROLE_MEMBER", $rolesUnder) ? $em->getRepository('TalisTrickPlayBundle:Role')->getByIdentifier("ROLE_MEMBER")->getName() : null,
            "ROLE_GUEST" => in_array("ROLE_GUEST", $rolesUnder) ? $em->getRepository('TalisTrickPlayBundle:Role')->getByIdentifier("ROLE_GUEST")->getName() : null,
            "ROLE_BANNED" => in_array("ROLE_BANNED", $rolesUnder) ? $em->getRepository('TalisTrickPlayBundle:Role')->getByIdentifier("ROLE_BANNED")->getName() : null
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
                $editable = in_array($userRole, $rolesUnder);

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
