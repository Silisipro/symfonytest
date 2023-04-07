<?php

namespace App\DataFixtures;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\User;
use App\Entity\Mark;
use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;
  
    

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }


    public function load(ObjectManager $manager): void
    {
        //user 
        $users =[];
        for ($i=0; $i <10 ; $i++) { 
            $user = new User();
            $user ->setFullName($this->faker->word())
                  ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName(): null)
                  ->setEmail($this->faker->email())
                  ->setRoles(['ROLE_USER'])
                 ->setPlainPassword('password');

            $users[] = $user;  
            $manager->persist($user);    
        }
 
        //Ingredient
         $ingredients = [];
        for ($i=0; $i <50 ; $i++) { 
        $ingredient = new Ingredient();
        $ingredient->setName($this->faker->word())
             ->setPrice(mt_rand(0,100))
             ->setUser($users[mt_rand(0, count($users) - 1)]);         

        $ingredients[]=$ingredient;
        $manager->persist($ingredient);    

        }
        
        //recipe
        $recipes = [];
        for ($j=0; $j <40 ; $j++) { 
           $recipe = new Recipe();
            $recipe->setName($this->faker->word())
               ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440): null)
               ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50): null)
               ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5): null)
               ->setDescription($this->faker->text(255))
               ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000): null)
               ->setIsFavorite(mt_rand(0, 1) == 1 ? true: false)
               ->setIsPublic(mt_rand(0, 1) == 1 ? true: false)
               ->setUser($users[mt_rand(0, count($users) - 1)]);  

            for ($k=0; $k < mt_rand(2, 10) ; $k++) { 
             $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
         }      
          $recipes[] = $recipe;     
         $manager->persist($recipe);    

        }
        //mark
        foreach ( $recipes as $recipe){
            for ($k=0; $k < mt_rand(0, 4) ; $k++){
                $mark = new Mark();
                $mark ->setMark(mt_rand(1, 5))
                      ->setUser($users[mt_rand(0, count($users) - 1)]) 
                      ->setRecipe($recipe);

               $manager->persist($mark);    
             }               
        }
            
        for ($i=0; $i <5 ; $i++) { 
            $contact = new Contact();
            $contact ->setFullName($this->faker->word())
                    ->setEmail($this->faker->email())
                    ->setSubject('Demande n°' . ($i + 1))
                 ->setMessage($this->faker->text());

               $manager->persist($contact);    
        }

        $manager->flush();
    }
}
