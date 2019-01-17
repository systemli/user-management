<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author doobry <doobry@systemli.org>
 */
class RecoveryResetPasswordType extends AbstractType
{
    const NAME = 'recovery_reset_password';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', HiddenType::class)
            ->add('recoveryToken', HiddenType::class)
            ->add('newPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'form.new-password'),
                'second_options' => array('label' => 'form.new-password_confirmation'),
                'invalid_message' => 'form.different-password',
            ))
            ->add('submit', SubmitType::class, array('label' => 'form.submit'));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'App\Form\Model\RecoveryResetPassword']);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }
}
