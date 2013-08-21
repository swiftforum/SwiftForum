<?php
/*
* This file is part of the Swift Forum package.
*
* (c) SwiftForum <https://github.com/swiftforum>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Talis\SwiftForumBundle\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Adds a flashbag message on login failure.
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class AuthenticationHandler implements AuthenticationFailureHandlerInterface
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $session = new Session();
        $referer = $request->headers->get('referer');
        $session->getFlashBag()->add(
            'error',
            $exception->getMessage()
        );

        return new RedirectResponse($referer);
    }
}