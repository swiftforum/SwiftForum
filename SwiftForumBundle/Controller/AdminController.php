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
use Talis\SwiftForumBundle\Form\Model\CreateForumBoard;
use Talis\SwiftForumBundle\Form\Model\CreateForumCategory;
use Talis\SwiftForumBundle\Form\Type\RoleType;
use Talis\SwiftForumBundle\Model\ForumBoard;
use Talis\SwiftForumBundle\Model\ForumCategory;
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
 * @todo: Figure out why the forum category form errors are not field-specific anymore
 * @todo: Add functionality to choose category color
 * @Route("/admin")
 * @author Felix Kastner <felix@chapterfain.com>
 */
class AdminController extends BaseController
{
    /**
     * Lets a admin add a board to a forum category.
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/forum/boards/new/{categoryId}", requirements={"categoryId" = "\d+"}, name="admin_forum_boards_add")
     */
    public function adminForumBoardsAddAction(Request $request, $categoryId)
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        /** @var ForumCategory $category */
        $category = $em->getRepository($this->getNameSpace() . ':ForumCategory')
            ->find($categoryId);

        if(!$category) {
            throw $this->createNotFoundException(
                'Category ' . $categoryId . ' does not exist.'
            );
        }

        $form = $this->createForm('talis_admin_create_forum_board', new CreateForumBoard(), array(
                'action' => $this->generateUrl('admin_forum_boards_add', array('categoryId' => $categoryId)),
                'method' => 'POST',
                'attr' => array('class' => 'form-inline')
            ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var ForumBoard $board */
            $board = $form->getData()->getForumBoard();
            if($form->getData()->getIconId()) {
                $iconid = $form->getData()->getIconId();

                $icon = $em->getRepository( $this->getNameSpace() . ':Icons')
                    ->find($iconid);

                $board->setIcon($icon);
            }
            $board->setCategory($category);

            $em->persist($board);
            $em->flush();
            $session->getFlashBag()->add(
                'success',
                'Board "' . $board->getName() . '" has been created in the "' . $category->getName() . '" category.');

            return $this->redirect($this->generateUrl('admin_forum_categories'));
        }

        return $this->render('TalisSwiftForumBundle:Admin/Forum:addboard.html.twig', array('form' => $form->createView(), 'category' => $category));
    }

    /**
     * Lets a admin edit the forum categories & boards. This entails adding, removing and changing the order of the categories and boards.
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/forum/categories", name="admin_forum_categories")
     */
    public function adminForumCategoriesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        $form = $this->createForm('talis_admin_create_forum_category', new CreateForumCategory(), array(
                'action' => $this->generateUrl('admin_forum_categories'),
                'method' => 'POST',
                'attr' => array('class' => 'form-inline')
            ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var ForumCategory $category */
            $category = $form->getData()->getForumCategory();
            if($form->getData()->getIconId()) {
                $iconid = $form->getData()->getIconId();

                $icon = $em->getRepository( $this->getNameSpace() . ':Icons')
                    ->find($iconid);

                $category->setIcon($icon);
            }

            $em->persist($category);
            $em->flush();
            $session->getFlashBag()->add(
                'success',
                'Category "' . $category->getName() . '" has been created.');
            return $this->redirect($this->generateUrl('admin_forum_categories'));
        }

        $categories = $em->getRepository($this->getNameSpace() . ':ForumCategory')
            ->getCategories();

        return $this->render('TalisSwiftForumBundle:Admin/Forum:categories.html.twig', array('form' => $form->createView(), 'categories' => $categories));
    }

    /**
     * Deletes a category
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/forum/categories/delete/{id}", requirements={"id" = "\d+"}, name="admin_forum_categories_delete")
     */
    public function adminForumCategoriesDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        $category = $em->getRepository($this->getNameSpace() . ':ForumCategory')
            ->find($id);

        if(!$category) {
            throw $this->createNotFoundException(
                'Category ' . $id . ' does not exist.'
            );
        }


        $em->remove($category);
        $em->flush();

        $session->getFlashBag()->add(
            'notice',
            'Category "' . $category->getName() . '" has been deleted.');

        return $this->redirect($this->generateUrl('admin_forum_categories'));
    }

    /**
     * Edits a category
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/forum/categories/edit/{id}", requirements={"id" = "\d+"}, name="admin_forum_categories_edit")
     */
    public function adminForumCategoriesEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        $category = $em->getRepository($this->getNameSpace() . ':ForumCategory')
            ->find($id);

        if(!$category) {
            throw $this->createNotFoundException(
                'Category ' . $id . ' does not exist.'
            );
        }

        $cat = new CreateForumCategory();
        $cat->setForumCategory($category);
        if(!is_null($category->getIcon())) {
            $cat->setIconId($category->getIcon()->getId());
        }

        $form = $this->createForm('talis_admin_create_forum_category', $cat, array(
                'action' => $this->generateUrl('admin_forum_categories_edit', array('id' => $id)),
                'method' => 'POST',
                'attr' => array('class' => 'form-inline')
            ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var ForumCategory $category */
            $category = $form->getData()->getForumCategory();
            if($form->getData()->getIconId()) {
                $iconid = $form->getData()->getIconId();

                $icon = $em->getRepository( $this->getNameSpace() . ':Icons')
                    ->find($iconid);

                $category->setIcon($icon);
            }

            $em->persist($category);
            $em->flush();
            $session->getFlashBag()->add(
                'success',
                'Category "' . $category->getName() . '" has been updated.');
            return $this->redirect($this->generateUrl('admin_forum_categories'));
        }

        return $this->render('TalisSwiftForumBundle:Admin/Forum:editcategory.html.twig', array('form' => $form->createView(), 'category' => $category));
    }

    /**
     * Shifts a category up / down
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/forum/categories/{direction}/{id}", requirements={"direction" = "up|down", "id" = "\d+"}, name="admin_forum_categories_offset")
     */
    public function adminForumCategoriesOffsetAction($direction, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        $category = $em->getRepository($this->getNameSpace() . ':ForumCategory')
            ->find($id);

        if(!$category) {
            throw $this->createNotFoundException(
                'Category ' . $id . ' does not exist.'
            );
        }

        $categories = $em->getRepository($this->getNameSpace() . ':ForumCategory')
            ->getCategories();

        try {

            $key = array_search($category, $categories);

            switch($direction)
            {
                // Move category up
                case 'up':
                    if($category->getId() == $categories[0]->getId()) {
                        throw new \LogicException(
                            'Category "' . $category->getName() . '" is already in first place.'
                        );
                    }

                    $beforeCat = $categories[($key-1)];
                    $afterCat = $category;
                    break;

                // Move category down
                case 'down':
                    if($category->getId() == end($categories)->getId()) {
                        throw new \LogicException(
                            'Category "' . $category->getName() . '" is already in last place.'
                        );
                    }

                    $beforeCat = $category;
                    $afterCat = $categories[($key+1)];
                    break;
            }

            $afterCat->setOrderOffset($afterCat->getOrderOffset() - 1);
            $beforeCat->setOrderOffset($beforeCat->getOrderOffset() + 1);
            $em->flush();
            $session->getFlashBag()->add(
                'success',
                'Category order changed'
            );
        } catch(\LogicException $error) {
            $session->getFlashBag()->add(
                'warning',
                $error->getMessage());
        }

        return $this->redirect($this->generateUrl('admin_forum_categories'));
    }


    /**
     * Deletes a board
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/forum/boards/delete/{id}", requirements={"id" = "\d+"}, name="admin_forum_boards_delete")
     */
    public function adminForumBoardsDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        $board = $em->getRepository($this->getNameSpace() . ':ForumBoard')
            ->find($id);

        if(!$board) {
            throw $this->createNotFoundException(
                'Board ' . $id . ' does not exist.'
            );
        }


        $em->remove($board);
        $em->flush();

        $session->getFlashBag()->add(
            'notice',
            'Board "' . $board->getName() . '" has been deleted.');

        return $this->redirect($this->generateUrl('admin_forum_categories'));
    }

    /**
     * Edits a board
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/forum/boards/edit/{id}", requirements={"id" = "\d+"}, name="admin_forum_boards_edit")
     */
    public function adminForumBoardsEditAction(Request $request, $id)
    {
        // Does not work yet. Todo: Make it work!
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        $board = $em->getRepository($this->getNameSpace() . ':ForumBoard')
            ->find($id);

        if(!$board) {
            throw $this->createNotFoundException(
                'Board ' . $id . ' does not exist.'
            );
        }

        $newBoard = new CreateForumBoard();
        $newBoard->setForumBoard($board);
        if(!is_null($board->getIcon())) {
            $newBoard->setIconId($board->getIcon()->getId());
        }

        $form = $this->createForm('talis_admin_create_forum_board', $newBoard, array(
                'action' => $this->generateUrl('admin_forum_boards_edit', array('id' => $id)),
                'method' => 'POST',
                'attr' => array('class' => 'form-inline')
            ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var ForumBoard $board */
            $board = $form->getData()->getForumBoard();
            if($form->getData()->getIconId()) {
                $iconid = $form->getData()->getIconId();

                $icon = $em->getRepository( $this->getNameSpace() . ':Icons')
                    ->find($iconid);

                $board->setIcon($icon);
            }

            $em->persist($board);
            $em->flush();
            $session->getFlashBag()->add(
                'success',
                'Board "' . $board->getName() . '" has been updated.');
            return $this->redirect($this->generateUrl('admin_forum_categories'));
        }

        return $this->render('TalisSwiftForumBundle:Admin/Forum:editboard.html.twig', array('form' => $form->createView(), 'board' => $board));
    }

    /**
     * Shifts a board up / down
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/forum/boards/{direction}/{category}/{id}", requirements={"direction" = "up|down", "category" = "\d+", "id" = "\d+"}, name="admin_forum_boards_offset")
     */
    public function adminForumBoardsOffsetAction($direction, $id)
    {
        // This does not work yet. Future todo: Make board shifting work.

//        $em = $this->getDoctrine()->getManager();
//        $session = new Session();
//
//        $board = $em->getRepository($this->getNameSpace() . ':ForumBoard')
//            ->find($id);
//
//        if(!$board) {
//            throw $this->createNotFoundException(
//                'Board ' . $id . ' does not exist.'
//            );
//        }
//
//

//        $boards = $em->getRepository($this->getNameSpace() . ':ForumBoard')
//            ->getBoards();
//
//        try {
//
//            $key = array_search($board, $boards);
//
//            switch($direction)
//            {
//                // Move board up
//                case 'up':
//                    if($board->getId() == $boards[0]->getId()) {
//                        throw new \LogicException(
//                            'Board "' . $board->getName() . '" is already in first place.'
//                        );
//                    }
//
//                    $beforeCat = $categories[($key-1)];
//                    $afterCat = $category;
//                    break;
//
//                // Move category down
//                case 'down':
//                    if($category->getId() == end($categories)->getId()) {
//                        throw new \LogicException(
//                            'Category "' . $category->getName() . '" is already in last place.'
//                        );
//                    }
//
//                    $beforeCat = $category;
//                    $afterCat = $categories[($key+1)];
//                    break;
//            }
//
//            $afterCat->setOrderOffset($afterCat->getOrderOffset() - 1);
//            $beforeCat->setOrderOffset($beforeCat->getOrderOffset() + 1);
//            $em->flush();
//            $session->getFlashBag()->add(
//                'success',
//                'Category order changed'
//            );
//        } catch(\LogicException $error) {
//            $session->getFlashBag()->add(
//                'warning',
//                $error->getMessage());
//        }

        return $this->redirect($this->generateUrl('admin_forum_categories'));
    }

    /**
     * Allows a officer to view a list of members.
     *
     * @Secure(roles="ROLE_OFFICER")
     * @Route("/members", name="admin_members")
     * @todo: The Role edit system still needs a overhaul. Current problems include:
     * @todo: Duplicate IDs
     * @todo: Required breaks W3C Validation
     * @todo: Problems may be solved through a "collection" type form
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
                $form = $this->createForm('talis_admin_role', $user, array(
                        'action' => $this->generateUrl('admin_edit_members', array('username' => rawurlencode($user->getUsername()))),
                        'method' => 'POST',
                        'attr' => array('class' => 'form-inline')));
                $formUsers[] = array('user' => $user, 'edit' => true, 'form' => $form->createView());
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

        /** @var User $user */
        $user = $em->getRepository( $this->getNameSpace() . ':User')
            ->findOneBy(array('username' => $username));

        if(!$user) {
            throw $this->createNotFoundException(
                'User does not exist.'
            );
        }

        // Check if the current logged in user is allowed to edit the permission of the target
        $permitted = $this->get('security.role_hierarchy')->getPermittedMap($this->getUser()->getRole()->getRole());

        if(!in_array($user->getRole()->getRole(), $permitted)) {
            return $this->redirect($this->generateUrl('admin_members'));
        }

        $form = $this->createForm('talis_admin_role', $user, array(
                'action' => $this->generateUrl('admin_edit_members', array('username' => rawurlencode($username))),
                'method' => 'POST'));


        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();
            $session->getFlashBag()->add(
                'success',
                'Role of ' . $user->getUsername() . ' has been changed to ' . $user->getRole()->getName() );
            return $this->redirect($this->generateUrl('admin_members'));
        }

        return $this->render('TalisSwiftForumBundle:Admin:editmembers.html.twig', array('user' => $user, 'form' => $form->createView()));
    }
} 