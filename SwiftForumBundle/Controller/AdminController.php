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
use Talis\SwiftForumBundle\Model\Role;
use Talis\SwiftForumBundle\Model\User;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * The AdminController lets users with authority perform administrative tasks on both forums as well as users.
 *
 * @todo: Allow quick role changing through adminMemberAction
 * @todo: Allow more administrative actions to be taken through adminEditMemberAction
 * @todo: Implement proper caching and cache invalidation
 * @Route("/admin")
 * @author Felix Kastner <felix@chapterfain.com>
 */
class AdminController extends BaseController
{
    /**
     * Lets a admin edit the forum categories. This entails adding, removing and changing the order of the categories.
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/forum/categories", name="admin_forum_categories")
     */
    public function adminForumCategoriesAction()
    {
        $categories = $this->getDoctrine()
            ->getRepository( $this->getNameSpace() . ':ForumCategory')
            ->findAll();

//        $form = $this->createFormBuilder($categories,array(
//                'action' => $this->generateUrl('admin_edit_members', array('username' => rawurlencode($username))),
//                'method' => 'POST'))
//            ->add('role', 'choice', array(
//                    'choices' => $permittedFixed,
//                    'expanded' => false,
//                    'multiple' => false,
//                    'required' => true,
//                    'label' => false,
//                    'data' => $user->getRole()->getId()))
//            ->add('Save', 'submit', array())
//            ->getForm();

        return $this->render('TalisSwiftForumBundle:Admin/Forum:categories.html.twig', array('categories' => $categories));
    }


    /**
     * Allows a officer to view a list of members.
     *
     * @Secure(roles="ROLE_OFFICER")
     * @Route("/members", name="admin_members")
     */
    public function adminMemberAction()
    {
        $role_map = $this->get('security.role_hierarchy')->getMap();

        $users = $this->getDoctrine()
            ->getRepository( $this->getNameSpace() . ':User')
            ->findAll();

        // $permitted is a array of all roles below the user's current one.
        $permitted = $role_map[$this->getUser()->getRole()->getRole()];
        $permitted[] = 'ROLE_WARNED';
        $permitted[] = 'ROLE_BANNED';

        $formUsers = array();

        /** @var User $user */
        foreach($users as $user) {
            if(in_array($user->getRole()->getRole(), $permitted)) {
                $formUsers[] = array('user' => $user, 'edit' => true);
            } else {
                $formUsers[] = array('user' => $user, 'edit' => false);
            }
        }

        return $this->render('TalisSwiftForumBundle:Admin:members.html.twig', array('users' => $formUsers));
    }

    /**
     * User-specific account changes.
     *
     * @Secure(roles="ROLE_OFFICER")
     * @Route("/members/{username}", name="admin_edit_members")
     */
    public function adminEditMemberAction($username, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        $username = rawurldecode($username);
        $role_map = $this->get('security.role_hierarchy')->getMap();

        /** @var User $user */
        $user = $em->getRepository( $this->getNameSpace() . ':User')
            ->findOneBy(array('username' => $username));

        $roles = $em->getRepository( $this->getNameSpace() . ':Role')->findAll();
        $roles = array_reverse($roles, true);

        // $permitted is a array of all roles below the user's current one.
        $permitted = $role_map[$this->getUser()->getRole()->getRole()];
        $permitted[] = 'ROLE_WARNED';
        $permitted[] = 'ROLE_BANNED';

        $permittedFixed = array();

        // We need to iterate over the roles to grab the proper role names.
        // While it would be marginally faster to iterate over $permitted, like this we can sort the result set.

        /** @var Role $role */
        foreach($roles as $role) {
            if(in_array($role->getRole(), $permitted)) {
                $permittedFixed[$role->getId()] = $role->getName();
            }
        }

        /** @todo: Create a proper form Model/Type */
        if(in_array($user->getRole()->getRole(), $permitted)) {
            $form = $this->createFormBuilder($roles,array(
                    'action' => $this->generateUrl('admin_edit_members', array('username' => rawurlencode($username))),
                    'method' => 'POST'))
                ->add('role', 'choice', array(
                        'choices' => $permittedFixed,
                        'expanded' => false,
                        'multiple' => false,
                        'required' => true,
                        'label' => false,
                        'data' => $user->getRole()->getId()))
                ->add('Save', 'submit', array())
                ->getForm();
        } else {
            return $this->redirect($this->generateUrl('admin_members'));
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $role = $em->getRepository( $this->getNameSpace() . ':Role')->find($form->getData()['role']);
            if($role && in_array($role->getRole(), $permitted)) {
                $user->setRole($role);
                $em->flush();
                $session->getFlashBag()->add(
                    'success',
                    'Role of ' . $user->getUsername() . ' has been changed to ' . $role->getName() );
                return $this->redirect($this->generateUrl('admin_members'));
            }
        }

        return $this->render('TalisSwiftForumBundle:Admin:editmembers.html.twig', array('user' => $user, 'form' => $form->createView()));
    }
} 