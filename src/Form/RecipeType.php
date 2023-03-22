<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Form\IngredientRepository;
use App\Entity\Recipe;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\VichimageType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RecipeType extends AbstractType
{

private $token;
public function __construct( TokenStorageInterface  $token)
{
    $this->token = $token;
}


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class,[ 
            'attr'=> [
                 'class'=>'form-control',
                 'minlength' =>'2',
                 'maxlength' =>'50'
                  ],
            'label'=> 'Nom',
            'label_attr'=>[
                    'class' =>'form-label mt-4'
                        ],
            'constraints'=> [
                new Assert\Length(['min'=>2, 'max'=>50]),
                new Assert\NotBlank()
                ]
                
            ])
            ->add('time',  IntegerType::class,[ 
                'attr'=> [
                     'class'=>'form-control',
                     'min' =>'1',
                     'max' =>'1440'
                      ],
                'label'=> 'Temps (en minutes)',
                'required' => false,
                'label_attr'=>[
                    'class' =>'form-label mt-4'
                    ],
                'constraints'=>[ 
                   new Assert\Positive(),
                   new Assert\LessThan(1441)
                    ] 
            ])
            ->add('nbPeople', IntegerType::class, [ 
                'attr'=> [
                     'class'=>'form-control',
                     'min' =>'1',
                     'max' =>'50'
                      ],
                'label'=> 'Nombre de personnes',
                'required' => false,
                'label_attr'=>[
                    'class' =>'form-label mt-4'
                    ],
                'constraints'=>[ 
                   new Assert\Positive(),
                   new Assert\LessThan(51)
                    ] 
            ])

            ->add('difficulty', RangeType::class, [ 
                'attr'=> [
                     'class'=>'form-range',
                     'min' =>'1',
                     'max' =>'5'
                      ],
                'label'=> 'Difficulté',
                'required' => false,
                'label_attr'=>[
                    'class' =>'form-label mt-4'
                    ],
                'constraints'=>[ 
                   new Assert\Positive(),
                   new Assert\LessThan(51)
                    ] 
            ])
            ->add('description', TextareaType::class, [ 
                'attr'=> [
                     'class'=>'form-control',
                     'min' =>'1',
                     'max' =>'5'
                      ],
                'label'=> 'Description',
                'label_attr'=>[
                    'class' =>'form-label mt-4'
                    ],
                'constraints'=>[ 
                    new Assert\NotBlank()
                    ] 
            ])
            ->add('price', MoneyType::class, [
                'attr'=>[
                    'class'=>'form-control',
                    'minlength' =>'2',
                    'maxlength' =>'50'
                    ],
                'label'=> 'Prix',
                'required' => false,
                'label_attr'=>[
                     'class' =>'form-label mt-4'
                        ],
                'constraints'=>[ 
                    new Assert\Positive(),
                    new Assert\LessThan(1001)
                    ]
                
            ]) 
            ->add('isFavorite', CheckboxType::class,  [
                
                'label'=> 'Favoris ?',
                'required' => false,
                'constraints'=>[ 
                    new Assert\NotNull(), 
                    ]    
            ] )
           // ->add('imageFile', VichimageType::class,  [
                
           //     'label'=> 'Image de la recette',
            //    'label_attr'=>[
            //        'class' =>'form-label mt-4'
             //          ],
           // ] )
            ->add('imageFile', FileType::class, [
                'label' => "Sélectionner vos images",
                'mapped' => false,
                'required' => false,
                'multiple' => true,
//               'constraints' => [
//                    new File([
//                        'maxSize' => '5120k', // 5 Mo
//                        'mimeTypes' => [
//                            'image/gif',
//                            'image/jpeg',
//                            'image/png',
//                            'image/svg+xml',
//                        ],
//                        'mimeTypesMessage' => "Veuillez choisir des images dont chacune d'elle a une taille maximale de 5 Mo",
//                    ])
//                ],
                'attr' => [
                    'class' => "form-control",
                    'label' => "Déposez vos fichiers ici",
                    'help' => "Ou cliquez pour les téléverser",
                    'is' => "drop-files",
                ],
            ])
            ->add('ingredients', EntityType::class, [
                'class'=>Ingredient::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')
                         ->where('i.user = :user')
                        ->orderBy('i.name', 'ASC')
                        ->setParameter('user', $this->token->getToken()->getuser());
                         },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label'=> 'Les ingredients',
                'label_attr'=>[
                     'class' =>'form-label mt-4'
                        ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' =>[
                    'class'=>'btn btn-primary mt-4',
                    ],
                'label'=> 'Créer ma recette'
            ])
            ;

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
