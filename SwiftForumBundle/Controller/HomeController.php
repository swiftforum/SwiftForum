<?php
/*
* This file is part of the Swift Forum package.
*
* (c) SwiftForum <https://github.com/swiftforum>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Talis\SwiftForumBundle\Controller;

use Talis\SwiftForumBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Displays the home page.
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class HomeController extends BaseController
{
    /**
     * Displays the home page.
     *
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        return $this->render('TalisSwiftForumBundle:Home:index.html.twig');
    }
} 