<?php

declare(strict_types=1);

/*
 * This file is part of the user bundle package.
 * (c) Connect Holland.
 */

namespace ConnectHolland\UserBundle\Form\Account;

use ConnectHolland\UserBundle\Entity\UserInterface;
use ConnectHolland\UserBundle\Security\PasswordConstraints;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as BasePasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @codeCoverageIgnore Contains no functionality as there is no buildForm, only configure methods are used.
 */
class AccountType extends AbstractType
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * @var PasswordConstraints
     */
    private $passwordConstraints;

    public function __construct(RegistryInterface $doctrine, PasswordConstraints $passwordConstraints)
    {
        $this->doctrine            = $doctrine;
        $this->passwordConstraints = $passwordConstraints;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'label'              => 'connectholland_user.registration.email',
                    'translation_domain' => 'ConnecthollandUserBundle',
                    'constraints'        => [
                        new NotBlank([
                            'message' => 'connectholland_user.validation.registration.email.blank',
                        ]),
                    ],
                ]
            )
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type'          => BasePasswordType::class,
                    'required'      => false,
                    'mapped'        => false,
                    'first_options' => [
                        'label'       => 'connectholland_user.reset.new_password.password',
                        'constraints' => $this->getPasswordConstraints(),
                    ],
                    'second_options' => [
                        'label' => 'connectholland_user.reset.new_password.password_repeat',
                    ],
                    'invalid_message'    => 'connectholland_user.validation.reset.new_password.password.repeat_invalid',
                    'translation_domain' => 'ConnecthollandUserBundle',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => $this->doctrine->getRepository(UserInterface::class)->getClassName(),
            'translation_domain' => 'ConnecthollandUserBundle',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    /**
     * Get all the constraints for password without any NotBlank constraint.
     * Password can be empty in this form, because the password field will be ignored unless it is filled in.
     */
    private function getPasswordConstraints(): array
    {
        $constraints = $this->passwordConstraints->getConstraints();
        foreach ($constraints as $key => $value) {
            if ($value instanceof NotBlank) {
                unset($constraints[$key]);
            }
        }

        return $constraints;
    }
}
