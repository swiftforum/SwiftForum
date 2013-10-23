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
use Talis\SwiftForumBundle\Form\Model\CreateForumPost;
use Talis\SwiftForumBundle\Form\Model\CreateForumTopic;
use Talis\SwiftForumBundle\Model\ForumBoard;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Talis\SwiftForumBundle\Model\ForumPost;
use Talis\SwiftForumBundle\Model\ForumTopic;

/**
 * The ForumController is used for basic forum functionality.
 *
 * @todo: Optimize SQL Query
 * @todo: Make trailing slashes optional everywhere via Route("/route/of/some/page{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"})
 * @author Felix Kastner <felix@chapterfain.com>
 */
class ForumController extends BaseController
{
    /**
     * Displays the forum index.
     *
     * @Route("/forum/", name="forum_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository($this->getNameSpace() . ':ForumCategory')
            ->getCategories();

        if(!$categories) {
            $categories = array();
        }

        return $this->render('TalisSwiftForumBundle:Forum:index.html.twig', array('categories' => $categories));
    }

    /**
     * Displays a board.
     *
     * @Route("/forum/{boardName}{separationDash}{boardId}/", requirements={"separationDash" = "-", "boardId" = "\d+"}, defaults={"separationDash" = "-"}, name="forum_view_board")
     * @Route("/forum/{boardId}/", requirements={"boardId" = "\d+"}, name="forum_view_board_short")
     */
    public function boardAction($boardId, $boardName = "")
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ForumBoard $board */
        $board = $em->getRepository($this->getNameSpace() . ':ForumBoard')
            ->find($boardId);

        if(!$board) {
            throw $this->createNotFoundException(
                'Board ' . $boardId . ' does not exist.'
            );
        }

        if ($board->getRole() && false === $this->get('security.context')->isGranted($board->getRole()->getRole())) {
            // No permission ? Give a 404.
            throw $this->createNotFoundException(
                'Board ' . $boardId . ' does not exist.'
            );
        }

        /* @todo Compare $boardName to the board's shortened name, and redirect if they don't match */

        return $this->render('TalisSwiftForumBundle:Forum:board.html.twig', array('board' => $board));
    }

    /**
     * Displays a topic.
     *
     * @Route("/forum/{boardName}{separationDash}{boardId}/{topicName}{separationDash2}{topicId}/", requirements={"separationDash" = "-", "separationDash2" = "-", "boardId" = "\d+", "topicId" = "\d+"}, defaults={"separationDash" = "-", "separationDash2" = "-"}, name="forum_view_topic")
     * @Route("/forum/{boardId}/{topicId}", requirements={"boardId" = "\d+", "topicId" = "\d+"}, name="forum_view_topic_short")
     */
    public function topicAction($boardId, $topicId, $topicName = "", $boardName = "")
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ForumTopic $topic */
        $topic = $em->getRepository($this->getNameSpace() . ':ForumTopic')
            ->find($topicId);

        if(!$topic) {
            throw $this->createNotFoundException(
                'Topic ' . $topicId . ' does not exist.'
            );
        }

        $board = $topic->getBoard();

        if ($board->getRole() && false === $this->get('security.context')->isGranted($board->getRole()->getRole())) {
            // No permission ? Give a 404.
            throw $this->createNotFoundException(
                'Topic ' . $topicId . ' does not exist.'
            );
        }

        /* @todo Compare $boardName to the board's shortened name,
         * compare $topicName to the topic's shortened name,
         * compare $board->getId() with $boardId:
         * If anything doesn't match, fix it and redirect. */

        return $this->render('TalisSwiftForumBundle:Forum:topic.html.twig', array('topic' => $topic, 'board' => $board));
    }

    /**
     * Create a new post. Must be logged in.
     *
     * @Secure(roles="ROLE_GUEST")
     * @Route("/forum/{boardName}{separationDash}{boardId}/{topicName}{separationDash2}{topicId}/create-post/", requirements={"separationDash" = "-", "separationDash2" = "-", "boardId" = "\d+", "topicId" = "\d+"}, defaults={"separationDash" = "-", "separationDash2" = "-"}, name="forum_create_post")
     */
    public function createPostAction(Request $request, $boardId, $topicId, $topicName = "", $boardName = "")
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        /** @var ForumTopic $topic */
        $topic = $em->getRepository($this->getNameSpace() . ':ForumTopic')
            ->find($topicId);

        if(!$topic) {
            throw $this->createNotFoundException(
                'Topic ' . $topicId . ' does not exist.'
            );
        }

        $board = $topic->getBoard();

        if ($board->getRole() && false === $this->get('security.context')->isGranted($board->getRole()->getRole())) {
            // No permission ? Give a 404.
            throw $this->createNotFoundException(
                'Topic ' . $topicId . ' does not exist.'
            );
        }

        /* @todo Compare $boardName to the board's shortened name,
         * compare $topicName to the topic's shortened name,
         * compare $board->getId() with $boardId:
         * If anything doesn't match, fix it and redirect. */

        $form = $this->createForm('talis_create_forum_post', new CreateForumPost(), array(
                'action' => $this->generateUrl('forum_create_post', array('boardName' => $boardName, 'boardId' => $boardId, 'topicName' => $topicName, 'topicId' => $topicId)),
                'method' => 'POST',
                'attr' => array('class' => 'form-inline')
            ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var ForumPost $post */
            $post = $form->getData()->getForumPost();

            $post->setCreator($this->getUser());
            $post->setTopic($topic);

            $em->persist($post);

            $topic->setLastPostDate(new \DateTime("now"));

            $em->flush();

            $session->getFlashBag()->add(
                'postsuccess',
                'You have replied to the "' . $topic->getTitle() . '" topic.');
            return $this->redirect($this->generateUrl('forum_view_topic', array('boardName' => $boardName, 'boardId' => $boardId, 'topicName' => $topicName, 'topicId' => $topicId)));
        }

        return $this->render('TalisSwiftForumBundle:Forum:create_post.html.twig', array('form' => $form->createView(), 'board' => $board, 'topic' => $topic));
    }

    /**
     * Create a new topic. Must be logged in.
     *
     * @Secure(roles="ROLE_GUEST")
     * @Route("/forum/{boardName}{separationDash}{boardId}/create-topic/", requirements={"separationDash" = "-", "boardId" = "\d+"}, defaults={"separationDash" = "-"}, name="forum_create_topic")
     */
    public function createTopicAction(Request $request, $boardId, $boardName = "")
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        /** @var ForumBoard $board */
        $board = $em->getRepository($this->getNameSpace() . ':ForumBoard')
            ->find($boardId);

        if(!$board) {
            throw $this->createNotFoundException(
                'Board ' . $boardId . ' does not exist.'
            );
        }

        if ($board->getRole() && false === $this->get('security.context')->isGranted($board->getRole()->getRole())) {
            // No permission ? Give a 404.
            throw $this->createNotFoundException(
                'Board ' . $boardId . ' does not exist.'
            );
        }

        /* @todo Compare $boardName to the board's shortened name, and redirect if they don't match */

        $form = $this->createForm('talis_create_forum_topic', new CreateForumTopic(), array(
                'action' => $this->generateUrl('forum_create_topic', array('boardName' => $boardName, 'boardId' => $boardId)),
                'method' => 'POST',
                'attr' => array('class' => 'form-inline')
            ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var ForumTopic $topic */
            $topic = $form->getData()->getForumTopic();
            /** @var ForumPost $post */
            $post = $form->getData()->getForumPost();

            if($form->getData()->getIconId()) {
                $iconid = $form->getData()->getIconId();

                $icon = $em->getRepository( $this->getNameSpace() . ':Icons')
                    ->find($iconid);

                $topic->setIcon($icon);
            }

            $topic->setBoard($board);
            $topic->setCreator($this->getUser());

            $em->persist($topic);

            $post->setCreator($this->getUser());
            $post->setTopic($topic);

            $em->persist($post);
            $em->flush();

            $session->getFlashBag()->add(
                'postsuccess',
                'You have created the "' . $topic->getTitle() . '" topic.');
            return $this->redirect($this->generateUrl('forum_view_board', array('boardName' => $boardName, 'boardId' => $boardId)));
        }

        return $this->render('TalisSwiftForumBundle:Forum:create_topic.html.twig', array('form' => $form->createView(), 'board' => $board));
    }

    /**
     * Deletes a topic and all associated posts
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/forum/{boardName}{separationDash}{boardId}/{topicName}{separationDash2}{topicId}/delete-topic/", requirements={"separationDash" = "-", "separationDash2" = "-", "boardId" = "\d+", "topicId" = "\d+"}, defaults={"separationDash" = "-", "separationDash2" = "-"}, name="forum_delete_topic")
     */
    public function deleteTopicAction($boardId, $topicId, $topicName = "", $boardName = "")
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ForumTopic $topic */
        $topic = $em->getRepository($this->getNameSpace() . ':ForumTopic')
            ->find($topicId);

        if(!$topic) {
            throw $this->createNotFoundException(
                'Topic ' . $topicId . ' does not exist.'
            );
        }

        $board = $topic->getBoard();

        if ($board->getRole() && false === $this->get('security.context')->isGranted($board->getRole()->getRole())) {
            // No permission ? Give a 404.
            throw $this->createNotFoundException(
                'Topic ' . $topicId . ' does not exist.'
            );
        }

        /* @todo Compare $boardName to the board's shortened name,
         * compare $topicName to the topic's shortened name,
         * compare $board->getId() with $boardId:
         * If anything doesn't match, fix it and redirect. */

        foreach ($topic->getPosts() AS $post) {
            $em->remove($post);
        }
        $em->remove($topic);
        $em->flush();

        return $this->redirect($this->generateUrl('forum_view_board', array('boardName' => $boardName, 'boardId' => $boardId)));
    }
}
