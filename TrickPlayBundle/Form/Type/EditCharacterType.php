<?php

namespace Talis\TrickPlayBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EditCharacterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('char_name', 'text', array('label' => 'Character Name'));
        $builder->add('char_job_primary', 'text', array('label' => 'Character\'s Primary Job'));
        $builder->add('char_job_secondary', 'text', array('label' => 'Character\'s Secondary Job'));
        $builder->add('char_prof_primary', 'text', array('label' => 'Character\'s Primary Profession'));
        $builder->add('char_prof_secondary', 'text', array('label' => 'Character\'s Secondary Profession'));
        $builder->add('Save Character', 'submit', array('attr' => array('formnovalidate' => 'true')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Talis\TrickPlayBundle\Entity\Character',
            ));
    }

    public function getName()
    {
        return 'characteredit';
    }
} 