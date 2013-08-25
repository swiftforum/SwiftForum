<?php

namespace Talis\TrickPlayBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
        $em = $this->getDoctrine()->getManager();
        $editable = true;
        $frontPage = $em->getRepository("TalisTrickPlayBundle:FrontPage")->get();
        return $this->render('TalisTrickPlayBundle:Home:index.html.twig', array("frontPage" => $frontPage, "editable" => $editable));
    }

    /**
     * @Route("/frontpage", name="edit_frontpage")
     * @Method({"POST"})
     */
    public function editFrontpageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $editable = true;
        $frontPage = $em->getRepository("TalisTrickPlayBundle:FrontPage")->get();
        return $this->render('TalisTrickPlayBundle:Home:index.html.twig', array("frontPage" => $frontPage, "editable" => $editable));
    }
}
