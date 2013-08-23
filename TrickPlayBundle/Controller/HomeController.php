<?php

namespace Talis\TrickPlayBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        $frontPage = $em->getRepository("TalisTrickPlayBundle:FrontPage")->get();
        return $this->render('TalisTrickPlayBundle:Home:index.html.twig', array("frontPage" => $frontPage));
    }
}
