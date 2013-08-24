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
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * The ForumController is used for basic forum functionality.
 *
 * @todo: Optimize SQL Query
 * @author Felix Kastner <felix@chapterfain.com>
 */
class ForumController extends BaseController
{
    /**
     * Displays the forum index.
     *
     * @Route("/forum", name="forum_index")
     * @Template()
     */
    public function indexAction()
    {
        $db = $this->getDoctrine()->getManager();

        $categories = $db->getRepository($this->getNameSpace() . ':ForumCategory')
            ->getCategories();

        return $this->render('TalisSwiftForumBundle:Forum:index.html.twig', array('categories' => $categories));
    }
}
