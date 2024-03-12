<?php

namespace App\Form;

use App\Entity\Ingredient;
use Faker\Provider\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;


class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'attr' => [
                    'class' => ' form-control ',
                    'minlength' => '2',
                    'maxlength' => "50",
                    'style' => 'margin-left: 20px ',

                ],
                'label' =>'Nom',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                    'style' => 'margin-left: 20px '

                ],
                'constraints'=>
                    [
                        new Assert\Length(['min'=>2, 'max'=>50]),
                        new Assert\NotBlank()
                    ]
            ])
            ->add('price', MoneyType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-left: 20px '
                ],
                'label' => 'Prix',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                    'style' => 'margin-left: 20px '

                ],
                'constraints'=> [
                    new Assert\Positive(),
                    new Assert\LessThan(200)
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4 mr-100',
                    'style' => 'margin-left: 20px '
                ],
                'label' => 'Créer un ingrédient'

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}