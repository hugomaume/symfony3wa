<?php


//check si tous les modules/formations sont bien listés avec leurs bouton de suppression

public function testGetAllTrainings()
{
    // Créer un client de test
    $client = static::createClient();

    // Envoyer une requête GET vers la page qui liste les formations
    $crawler = $client->request('GET', '/list_trainings');

    // Récupérer toutes les formations depuis la base de données
    $em = self::$kernel->getContainer()->get('doctrine')->getManager();
    $trainings = $em->getRepository(Training::class)->findAll();

    // Vérifier que la réponse est bien 200 (OK)
    $this->assertEquals(200, $client->getResponse()->getStatusCode());

    // Vérifier que le nombre de formations affichées correspond au nombre de formations en base de données
    $this->assertCount(count($trainings), $crawler->filter('tbody > tr'));
}


// test qui check si un module de supprime bien 

public function testDeleteTrainingById()
{
    // Créer un client de test
    $client = static::createClient();

    // Récupérer une formation depuis la base de données
    $em = self::$kernel->getContainer()->get('doctrine')->getManager();
    $training = $em->getRepository(Training::class)->findOneBy([]);

    // S'assurer qu'il existe au moins une formation pour effectuer le test
    $this->assertNotNull($training);

    // Envoyer une requête GET pour supprimer la formation
    $crawler = $client->request('GET', '/delete_training/' . $training->getId());

    // Vérifier que la réponse est bien 200 ou une redirection
    $this->assertEquals(200, $client->getResponse()->getStatusCode());

    // Suivre la redirection après suppression (optionnel si redirection)
    if ($client->getResponse()->isRedirect()) {
        $crawler = $client->followRedirect();
    }

    // Vérifier que la formation a bien été supprimée en base de données
    $em->clear();  // Nettoyer l'EntityManager
    $deletedTraining = $em->getRepository(Training::class)->find($training->getId());
    $this->assertNull($deletedTraining);
}



public function testVerifyTrainingDeletion()
{
    // Créer un client de test
    $client = static::createClient();

    // Récupérer une formation à supprimer depuis la base de données
    $em = self::$kernel->getContainer()->get('doctrine')->getManager();
    $training = $em->getRepository(Training::class)->findOneBy([]);

    // S'assurer qu'il existe au moins une formation pour effectuer le test
    $this->assertNotNull($training);

    // Stocker l'ID de la formation à supprimer
    $trainingId = $training->getId();

    // Supprimer la formation
    $client->request('GET', '/delete_training/' . $trainingId);

    // Suivre la redirection après suppression (si elle existe)
    if ($client->getResponse()->isRedirect()) {
        $client->followRedirect();
    }

    // Récupérer toutes les formations depuis la base de données après suppression
    $trainings = $em->getRepository(Training::class)->findAll();

    // Vérifier que l'ID de la formation supprimée n'est plus dans la liste des formations
    foreach ($trainings as $remainingTraining) {
        $this->assertNotEquals($trainingId, $remainingTraining->getId());
    }
}



