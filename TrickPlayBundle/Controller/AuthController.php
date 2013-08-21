<?php

namespace Talis\TrickPlayBundle\Controller;

use Talis\SwiftForumBundle\Controller\AuthController as AuthControllerBase;
use Talis\TrickPlayBundle\Entity\Character;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Talis\TrickPlayBundle\Form\Type\EditCharacterType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Description
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */

/**
 * @Route("/")
 */
class AuthController extends AuthControllerBase
{

    /**
     * @Route("/editprofile", name="auth_edit")
     * @Secure(roles="IS_AUTHENTICATED_FULLY, IS_AUTHENTICATED_REMEMBERED")
     */
    public function editProfileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $character = $em->getRepository( $this->getNameSpace() . ':Character')
            ->findOneBy(array('user' => $this->getUser(), 'isPrimary' => true));

        if(!$character) {
            $character = new Character();
            $character->setUser($this->getUser());
        }

        $form = $this->createForm(new EditCharacterType(), $character, array(
                'action' => $this->generateUrl('auth_edit'),
                'method' => 'POST'));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $session = new Session();

            if( $character->getCharName() == '' &&
                $character->getCharJobPrimary()  == '' &&
                $character->getCharJobSecondary()  == '' &&
                $character->getCharProfPrimary()  == '' &&
                $character->getCharProfSecondary() == '') {
                $em->remove($character);
                $em->flush();
                $session->getFlashBag()->add(
                    'notice',
                    'Your Primary Character has been deleted.');
                return $this->redirect($this->generateUrl('home'));
            }

            $em->persist($character);
            $em->flush();

            $session->getFlashBag()->add(
                'success',
                'Your Primary Character has been edited.');
            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('TalisTrickPlayBundle:Auth:editprofile.html.twig',
            array('form' => $form->createView())
        );
    }
} 