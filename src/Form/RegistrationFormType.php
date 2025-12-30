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
                "label" => "register.username",
                "attr" => [
                    "class" => "form-input",
                    "placeholder" => " ",
                    "required" => "",
                ],
                "constraints" => [
                    new NotBlank(message: "register.username_required"),
                    new Length(
                        min: 3,
                        max: 50,
                        minMessage: "register.username_min",
                        maxMessage: "register.username_max",
                    ),
                ]
            ])
            ->add("email", EmailType::class, [
                "label" => "register.email",
                "attr" => [
                    "class" => "form-input",
                    "placeholder" => " ",
                    "required" => "",
                ],
                "constraints" => [
                    new NotBlank(message: "register.email_required"),
                ]
            ])
            ->add("agreeTerms", CheckboxType::class, [
                "mapped" => false,
                "label" => "register.agree_terms",
                "constraints" => [
                    new NotBlank(message: "register.agree_terms_required"),
                ],
            ])
            ->add("plainPassword", PasswordType::class, [
                "mapped" => false,
                "label" => "register.password",
                "attr" => [
                    "autocomplete" => "new-password",
                    "class" => "form-input",
                    "placeholder" => " ",
                    "required" => "",
                ],
                "constraints" => [
                    new NotBlank(message: "register.password_required"),
                    new Length(
                        min: 6,
                        max: 4096,
                        minMessage: "register.password_min",
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "data_class" => User::class,
            "translation_domain" => "messages",
        ]);
    }
}
