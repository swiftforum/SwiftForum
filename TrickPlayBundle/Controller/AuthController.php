<?php

namespace Talis\TrickPlayBundle\Controller;

use Talis\SwiftForumBundle\Controller\AuthController as AuthControllerBase;
use Talis\TrickPlayBundle\Entity\Character;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Talis\TrickPlayBundle\Form\Type\EditCharacterType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * @Route("/lodestone/characters", name="auth_characters")
     * @Secure(roles="ROLE_MEMBER")
     */
    public function lodestoneCharactersAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lodestone = $this->get("Lodestone");
        $query = $this->getRequest()->get("query", "");

        // Must have 3 characters or more
        if (strlen($query) < 3) return new JsonResponse(array("error" => "Query must have at least 3 characters"), 400);

        $characters = $lodestone->searchCharacters($query);
        return new JsonResponse($characters);
    }

    /**
     * @Route("/editprofile", name="auth_edit")
     * @Secure(roles="IS_AUTHENTICATED_FULLY, IS_AUTHENTICATED_REMEMBERED")
     */
    public function editProfileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $permitted = $this->get('security.role_hierarchy')->getPermittedMap($this->getUser()->getRole()->getRole());
        $lodestone = $this->get("Lodestone");
        $characterRepository = $em->getRepository('TalisTrickPlayBundle:LodestoneCharacter');
        $characterId = Request::createFromGlobals()->request->get("character", null);
        $user = $this->getUser();

        // Only members and up can change their characters; they can only do so when their character hasn't already been set
        if (in_array("ROLE_GUEST", $permitted) && !$user->getCharacter() && $characterId) {
            $character = $characterRepository->get($characterId);

            // Get latest Lodestone data
            $lodestoneData = $lodestone->getCharacter($characterId);

            var_dump($lodestoneData);
            $character->set($lodestoneData, true);
            $character->setUser($user);

            // Persist
            $em->persist($character);
            $em->flush();

            // Success message
            $session = new Session();
            $session->getFlashBag()->add('success', 'Your character has been set');
            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('TalisTrickPlayBundle:Auth:editprofile.html.twig');
    }
}
