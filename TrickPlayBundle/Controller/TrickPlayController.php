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
     * @Route("/guides", name="ffxiv_guides")
     */
    public function guideAction()
    {
        return $this->render('TalisTrickPlayBundle:TrickPlay:guides.html.twig');
    }

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
        $em = $this->get("Doctrine")->getManager();
        $members = $em->getRepository('TalisTrickPlayBundle:LodestoneCharacter')->findAll();

        return $this->render('TalisTrickPlayBundle:TrickPlay:roster.html.twig', array(
            "members" => $members
        ));
    }
}
