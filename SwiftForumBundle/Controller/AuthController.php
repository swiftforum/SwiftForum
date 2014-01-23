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
use Talis\SwiftForumBundle\Form\Model\ForgotPassword;
use Talis\SwiftForumBundle\Form\Model\ResetPassword;
use Talis\SwiftForumBundle\Form\Type\ForgotPasswordType;
use Talis\SwiftForumBundle\Form\Type\RegistrationType;
use Talis\SwiftForumBundle\Form\Model\Registration;
use Talis\SwiftForumBundle\Form\Type\ResetPasswordType;
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
     * @Route("/verify/{code}", name="auth_verify")
     */
    public function verifyAction($code)
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        /** @var User $user */
        $user = $em->getRepository( $this->getNameSpace() . ':User')
            ->findOneBy(array('token' => $code));

        if (!$user) {
            $session->getFlashBag()->add(
                'error',
                'Invalid email confirmation token.');
        } elseif ($user->isEnabled()) {
            $session->getFlashBag()->add(
                'notice',
                'Account has already been activated.');
        } else {
            $user->setIsActive(true);
            $user->setToken(null);
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

            $generator = $this->container->get('security.secure_random');
            $token = rtrim(strtr(base64_encode($generator->nextBytes(32)), '+/', '-_'), '=');

            $user->setToken($token);

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
                            'confirm_code' => $user->getToken())
                    ), 'text/plain')
                ->addPart(                    $this->renderView(
                        'TalisSwiftForumBundle:Email:registration.html.twig',
                        array(
                            'name'         => $user->getUsername(),
                            'confirm_code' => $user->getToken())
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

    /**
     * Forgot Password - Password Change
     *
     * @Route("/reset/{code}", name="auth_reset")
     */
    public function resetPasswordAction(Request $request, $code)
    {
        // Send logged-in users who try to access this page to Home
        if( ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === true) ||
            ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') === true) ) {
            return $this->redirect($this->generateUrl('home'));
        }

        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        /** @var User $user */
        $user = $em->getRepository( $this->getNameSpace() . ':User')
            ->findOneBy(array('token' => $code));

        if(!$user) {
            $session->getFlashBag()->add(
                'error',
                'Invalid password reset token.');
            return $this->redirect($this->generateUrl('home'));
        }

        $resetPassword = new ResetPassword();
        $resetPassword->setUser($user);

        $form = $this->createForm(new ResetPasswordType($this->getPath()), $resetPassword, array(
                'action' => $this->generateUrl('auth_reset', array('code' => $code)),
                'method' => 'POST'
            ));

        $form->handleRequest($request);

        if($form->isValid()) {
            $resetPasswordFormData = $form->getData();

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);

            $password = $encoder->encodePassword($resetPasswordFormData->getPassword(), $user->getSalt());
            $user->setPassword($password);
            $user->setToken(null);

            $em->flush();

            $session->getFlashBag()->add(
                'success',
                'Your password has been changed, and you can log in.');

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('TalisSwiftForumBundle:Auth:reset.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Forgot Password Initial Form
     *
     * @Route("/forgot", name="auth_forgot")
     */
    public function forgotPasswordFormAction(Request $request)
    {
        // Send logged-in users who try to access this page to Home
        if( ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === true) ||
            ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') === true) ) {
            return $this->redirect($this->generateUrl('home'));
        }

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new ForgotPasswordType($this->getPath()), new ForgotPassword(), array(
                'action' => $this->generateUrl('auth_forgot'),
                'method' => 'POST'));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $forgotPassword = $form->getData();

            $email = $forgotPassword->getEmail();

            $session = new Session();

            /** @var User $user */
            $user = $em->getRepository( $this->getNameSpace() . ':User')
                ->findOneBy(array('email' => $email));

            if($user && $user->getToken() === null) {
                // User Exist and no Token currently exists - we can send the recovery mail.

                $generator = $this->container->get('security.secure_random');
                $token = rtrim(strtr(base64_encode($generator->nextBytes(32)), '+/', '-_'), '=');

                $user->setToken($token);
                $em->flush();

                $message = \Swift_Message::newInstance()
                    ->setSubject('SwiftForum Password Recovery')
                    ->setFrom(array('noreply@chapterfain.com' => 'SwiftForum'))
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'TalisSwiftForumBundle:Email:forgot.txt.twig',
                            array(
                                'name'         => $user->getUsername(),
                                'confirm_code' => $user->getToken())
                        ), 'text/plain')
                    ->addPart(                    $this->renderView(
                            'TalisSwiftForumBundle:Email:forgot.html.twig',
                            array(
                                'name'         => $user->getUsername(),
                                'confirm_code' => $user->getToken())
                        ), 'text/html')
                ;
                $this->get('mailer')->send($message);

                $session->getFlashBag()->add(
                    'success',
                    'A email containing instructions on how to reset your password has been sent to "' . $user->getEmail() . '" (remember to check spam)!');
            } else if($user) {
                // User exists, but a token already exists.
                $session->getFlashBag()->add(
                    'notice',
                    'Your account has already requested a password reset. Please check your inbox for "' . $user->getEmail() . '" and click on the reset link (remember to check spam)!');
            } else {
                $session->getFlashBag()->add(
                    'notice',
                    'No accounts with this email address exist.');
            }

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('TalisSwiftForumBundle:Auth:forgot.html.twig',
            array('form' => $form->createView())
        );
    }

} 