<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("username", TextType::class, [
                "mapped" => false,
                "label" => "Username",
                "attr" => ["placeholder" => "Username"],
                "constraints" => [new NotBlank(message: "Please enter a username"),
                    new Length(
                        min: 3,
                        max: 50,
                        minMessage: "Your username should be at least {{ limit }} characters",
                        maxMessage: "Your username cannot exceed {{ limit }} characters",
                    ),
                ]
            ])
            ->add("email", EmailType::class, [
                "attr" => ["placeholder" => "Email"],
                "constraints" => [new NotBlank(message: "Please enter an email"),]
            ])
            ->add("agreeTerms", CheckboxType::class, [
                "mapped" => false,
                "constraints" => [
                    new NotBlank(message: "You should agree to our terms."),
                ],
            ])
            ->add("plainPassword", PasswordType::class, [
                "mapped" => false,
                "attr" => [
                    "autocomplete" => "new-password",
                    "placeholder" => "Password",
                    ],
                "constraints" => [
                    new NotBlank(message: "Please enter a password"),
                    new Length(
                        min: 6,
                        max: 4096,
                        minMessage: "Your password should be at least {{ limit }} characters",
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
