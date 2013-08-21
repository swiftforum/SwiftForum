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
use Talis\SwiftForumBundle\Form\Type\RegistrationType;
use Talis\SwiftForumBundle\Form\Model\Registration;
use Talis\SwiftForumBundle\Model\User;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Util\StringUtils;

/**
 * The AuthController handles account tasks such as:
 * Logging in, out, verifying your email, registering a new account and editing your account.
 *
 * @todo: Implement proper caching and cache invalidation
 * @todo: The email activation code might need to be overhauled; It currently is based on the accounts salt + username + email.
 * @todo: I'm not entirely sure if using the passwords salt for it presents a security risk or not.
 * @Route("/")
 * @author Felix Kastner <felix@chapterfain.com>
 */
class AuthController extends BaseController
{
    /**
     * @Route("/login_check", name="auth_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="auth_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }

    /**
     * Activates the account if a proper activation code has been supplied.
     *
     * @Route("/verify/{username}/{code}", name="auth_verify")
     */
    public function verifyAction($username, $code)
    {
        $username = rawurldecode($username);
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        /** @var User $user */
        $user = $em->getRepository( $this->getNameSpace() . ':User')
            ->findOneBy(array('username' => $username));

        if (!$user || !StringUtils::equals($code, sha1($user->getSalt() . $user->getUsername() . $user->getEmail()))) {
            $session->getFlashBag()->add(
                'error',
                'Invalid email confirmation token.');
        } elseif ($user->isEnabled()) {
            $session->getFlashBag()->add(
                'notice',
                'Account has already been activated.');
        } else {
            $user->setIsActive(true);
            $em->flush();

            $session->getFlashBag()->add(
                'success',
                'Your account has been activated, and you can log in.');
        }

        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * Is used to redirect after login.
     *
     * @Route("/login", name="auth_login")
     */
    public function loginAction()
    {
        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * Account registration.
     *
     * @Route("/register", name="auth_register")
     */
    public function registerFormAction(Request $request)
    {
        if( ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === true) ||
            ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') === true) ) {
            return $this->redirect($this->generateUrl('auth_logout'));
        }

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new RegistrationType($this->getPath()), new Registration(), array(
                'action' => $this->generateUrl('auth_register'),
                'method' => 'POST'));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $factory = $this->get('security.encoder_factory');

            $registration = $form->getData();

            /** @var User $user */
            $user = $registration->getUser();

            $role = $em->getRepository( $this->getNameSpace() . ':Role')
                ->findOneBy(array('role' => 'ROLE_GUEST'));

            $user->setRole($role);

            $encoder = $factory->getEncoder($user);

            $password = $encoder->encodePassword($registration->getPassword(), $user->getSalt());
            $user->setPassword($password);

            $session = new Session();

            $em->persist($user);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('SwiftForum Registration Confirmation')
                ->setFrom(array('noreply@chapterfain.com' => 'SwiftForum'))
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'TalisSwiftForumBundle:Email:registration.txt.twig',
                        array(
                            'name'         => $user->getUsername(),
                            'confirm_code' => sha1($user->getSalt() . $user->getUsername() . $user->getEmail()))
                    ), 'text/plain')
                ->addPart(                    $this->renderView(
                        'TalisSwiftForumBundle:Email:registration.html.twig',
                        array(
                            'name'         => $user->getUsername(),
                            'confirm_code' => sha1($user->getSalt() . $user->getUsername() . $user->getEmail()))
                    ), 'text/html')
            ;
            $this->get('mailer')->send($message);

            $session->getFlashBag()->add(
                'success',
                'Your account has been created. Please check your inbox for "' . $user->getEmail() . '" and click on the confirmation link (remember to check spam)!');

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('TalisSwiftForumBundle:Auth:register.html.twig',
            array('form' => $form->createView())
        );
    }
} 