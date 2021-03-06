<?php

namespace Talis\TrickPlayBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Talis\SwiftForumBundle\Controller\HomeController as HomeControllerBase;
use Talis\TrickPlayBundle\Entity\FrontPage;

/**
 * Description
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class HomeController extends HomeControllerBase
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        // Only officers or higher can edit frontpage
        $currentUser = $this->get('security.context')->getToken()->getUser();
        $editable = gettype($currentUser) == "object" && $currentUser->getRole();
        $editable = $editable && ($currentUser->getRole()->getRole() == "ROLE_OFFICER" || in_array("ROLE_OFFICER", $this->get('security.role_hierarchy')->getPermittedMap($currentUser->getRole()->getRole())));

        $em = $this->getDoctrine()->getManager();
        $frontPage = $em->getRepository("TalisTrickPlayBundle:FrontPage")->get();
        $company = $em->getRepository("TalisTrickPlayBundle:LodestoneFreeCompany")->get();

        return $this->render('TalisTrickPlayBundle:Home:index.html.twig', array("frontPage" => $frontPage, "editable" => $editable, "company" => $company));
    }

    /**
     * @Route("/frontpage", name="edit_frontpage")
     * @Secure(roles="ROLE_OFFICER")
     * @Method({"POST"})
     */
    public function editFrontpageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $markdown = Request::createFromGlobals()->request->get("markdown", "");
        $currentUser = $this->get('security.context')->getToken()->getUser();

        // Update frontpage
        $frontPage = $em->getRepository("TalisTrickPlayBundle:FrontPage")->get();
        $frontPage->setMarkdown($markdown, $currentUser);
        $em->persist($frontPage);
        $em->flush();

        return new JsonResponse(array("lastEdit" => $frontPage->getLastEdit(), "lastEditor" => array("username" => $frontPage->getLastEditor()->getUsername(), "url" => $frontPage->getLastEditor()->getUrl())));
    }
}
