<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Tag;
use App\Factory\AnswerFactory;
use App\Factory\QuestionFactory;
use App\Factory\QuestionTagFactory;
use App\Factory\TagFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Clue\StreamFilter\fun;

class AppFixtures extends Fixture
{
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        TagFactory::createMany(100);

       $questions = QuestionFactory::new()->createMany(20, function() {
           return [
               'questionTags' => QuestionTagFactory::new(function () {
                    return [
                        'tag' => TagFactory::random(),
                    ];
               })->many(1, 5),
           ];
       });

//       QuestionTagFactory::createMany(100, function () {
//          return [
//            'tag' => TagFactory::random(),
//            'question' => QuestionFactory::random(),
//          ];
//       });

       QuestionFactory::new()
           ->unpublished()
           ->many(5)
           ->create();

       AnswerFactory::new()
           ->many(100)
           ->create(function () use ($questions) {
               return [
                   'question' => $questions[array_rand($questions)]
               ];
           }
       );

//        AnswerFactory::new()
//            ->many(100)
//            ->create();

//        $question = QuestionFactory::createOne(['name' => 'Some name']);
//        $answer1 = new Answer();
//        $answer1->setContent('answer 1');
//        $answer1->setUsername('gvenik');
//
//        $answer2 = new Answer();
//        $answer2->setContent('answer 2');
//        $answer2->setUsername('simonik');
//
//        $question->addAnswer($answer1);
//        $question->addAnswer($answer2);
//
//        $manager->persist($answer1);
//        $manager->persist($answer2);

        AnswerFactory::new(function () use ($questions) {
            return [
                'question' => $questions[array_rand($questions)]
            ];
        })->needsApproval()->many(20)->create();

        //$question = QuestionFactory::createOne()->object();

//        $tag1 = new Tag();
//        $tag1->setName('dinosaurs');
//        $tag2 = new Tag();
//        $tag2->setName('monster trucks');
//
//        $tag1->addQuestion($question);
//        $tag2->addQuestion($question);

//        $manager->persist($tag1);
//        $manager->persist($tag2);

        UserFactory::createOne([
            'email' => 'abraca_admin@example.com',
            'roles' => ['ROLE_ADMIN']
            ]);

        UserFactory::createOne([
            'email' => 'abraca_user@example.com',
        ]);

        UserFactory::createMany(10);

       $manager->flush();
    }
}
