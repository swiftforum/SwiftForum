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
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * The BasicController offers access to easily cachable, non-user/auth related content, such as providing a JSON-list of
 * fontawesome icons.
 *
 * @todo: Optimize SQL Query
 * @author Felix Kastner <felix@chapterfain.com>
 */
class BasicController extends BaseController
{
    /**
     * Return the list of icons in JSON format.
     *
     * @Route("/icons", name="basic_icons")
     */
    public function iconsAction()
    {
        $em = $this->getDoctrine()->getManager();

        if($this->getDoctrine()->getManager()->getConfiguration()->getResultCacheImpl()->contains('icons')) {
            $icons = $this->getDoctrine()->getManager()->getConfiguration()->getResultCacheImpl()->fetch('icons');
        } else {
            $icons = $em->getRepository($this->getNameSpace() . ':Icons')
                ->getIcons();
        }

        $response = new JsonResponse();
        $response->setData($icons);

        $response->setCache(array(
            'etag'          => md5($response->getContent()),
            'max_age'       => 86400, // Valid for 1 day
            's_maxage'      => 86400,
            'public'        => true
        ));

        $response->isNotModified($this->getRequest());

        return $response;
    }
} 