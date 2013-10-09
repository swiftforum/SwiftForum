<?php
/*
* This file is part of the Swift Forum package.
*
* (c) SwiftForum <https://github.com/swiftforum>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Talis\SwiftForumBundle\Form\Type;

use Talis\SwiftForumBundle\Model\Role;
use Talis\SwiftForumBundle\Model\RoleRepository;
use Talis\SwiftForumBundle\Model\User;
use Talis\SwiftForumBundle\Service\RoleHierarchy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Forum Board Role type
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class ForumBoardRoleType extends AbstractType
{
    private $entityNameSpace;
    private $entityPath;
    private $securityContext;
    private $roleMap;

    /**
     * Constructor.
     *
     * @param SecurityContext $securityContext
     * @param RoleHierarchy $roleMap
     * @param $tsforum
     */
    public function __construct(SecurityContext $securityContext, RoleHierarchy $roleMap, $tsforum)
    {
        $this->entityNameSpace = $tsforum['namespace'];
        $this->entityPath = $tsforum['path'];
        $this->securityContext = $securityContext;
        $this->roleMap = $roleMap;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->securityContext->getToken()->getUser();
        if (!$user) {
            throw new \LogicException(
                'The ForumBoardRoleType cannot be used without an authenticated user!'
            );
        }

        $factory = $builder->getFormFactory();
        $entityNameSpace = $this->entityNameSpace;

        $permitted = $this->roleMap->getPermittedMap($user->getRole()->getRole());

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use($user, $factory, $entityNameSpace, $permitted){
                $form = $event->getForm();

                $formOptions = array(
                    'class'           => $entityNameSpace . ':Role',
                    'property'        => 'name',
                    'auto_initialize' => false,
                    'required'        => false,
                    'empty_value'     => 'Unrestricted Access',
                    'attr'            => array('class' => 'form-control', 'style' => 'pull-right'),
                    'query_builder'   => function(RoleRepository $er) use ($permitted) {
                        return $er->getPermittedRoles($permitted);
                    }
                );

                $form->add($factory->createNamed('role', 'entity', null, $formOptions));
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => $this->entityPath . '\Entity\Role',
            ));
    }

    public function getName()
    {
        return 'talis_admin_forum_board_role';
    }
} 
