<?php

class SchoolFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = FakerFactory::create(); // Initialize Faker

        $schools = [];

        for( $i = 0; $i < 3 ; $i++ )
        {
            $school = new School();
            $school->setName($faker->company());
            $school->setDescription($faker->sentence());

            $manager->persist($school);

            $schools[] = $school;
        }

        $modules = [];

        for( $i = 0; $i < 6 ; $i++ )
        {
            $randomSchool = $schools[array_rand($schools)];
            $module = new Module();
            $module->setName($faker->word());
            $module->setDescription($faker->sentence());

            $manager->persist($module);
            $modules[] = $module;
        }

        for( $i = 0; $i < 6 ; $i++ )
        {
            $randomSchool = $schools[array_rand($schools)];
            $training = new Training();
            $training->setName($faker->word());
            $training->setDescription($faker->sentence());
            $training->setSchool($randomSchool);

            $randomModules = array_rand($modules,3);

            foreach( $randomModules as $module )
            {
                $training->addModule($modules[$module]);
            }

            $manager->persist($training);

            $trainings[] = $training;
        }

        $manager->flush();
    }
}