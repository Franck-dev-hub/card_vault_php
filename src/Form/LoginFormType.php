<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;


class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("email", EmailType::class, [
                "mapped" => false,
                "label" => "login.email",
                "attr" => [
                    "class" => "security-input",
                    "placeholder" => " ",
                    "autocomplete" => "email",
                    "required" => "",
                ],
                "constraints" => [
                    new NotBlank(message: "login.email_required"),
                ],
            ])
            ->add("password", PasswordType::class, [
                "mapped" => false,
                "label" => "login.password",
                "attr" => [
                    "class" => "security-input",
                    "placeholder" => " ",
                    "autocomplete" => "current-password",
                    "required" => "",
                ],
                "constraints" => [
                    new NotBlank(message: "login.password_required"),
                ],
            ])
            ->add("_remember_me", CheckboxType::class, [
                "mapped" => false,
                "label" => "login.remember_me",
                "required" => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "translation_domain" => "messages",
        ]);
    }
}
