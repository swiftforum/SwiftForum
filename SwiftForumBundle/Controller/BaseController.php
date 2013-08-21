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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * The BaseController extends the default Symfony controller and makes a few functions available to all other controllers.
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class BaseController extends Controller
{
    /**
     * Get the namespace parameter, which is prepended to all doctrine database calls to use the proper entities.
     *
     * @return mixed
     */
    public function getNameSpace()
    {
        return $this->container->getParameter('tsforum')['namespace'];
    }

    /**
     * Get the path which is used to find entities in the file system.
     *
     * @return mixed
     */
    public function getPath()
    {
        return $this->container->getParameter('tsforum')['path'];
    }
} 