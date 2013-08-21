<?php

namespace Talis\TrickPlayBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Talis\SwiftForumBundle\Controller\HomeController as HomeControllerBase;

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
        return $this->render('TalisTrickPlayBundle:Home:index.html.twig');
    }
} 