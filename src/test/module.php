<?php

namespace Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Training;
use App\Entity\Module;

class TrainingModuleTest extends WebTestCase
{
    // test pour vérifier que tous les modules de tous les trainings sont bien affichés
    public function testDisplayModules()
    {

        $client = static::createClient();

        $em = self::$kernel->getContainer()->get('doctrine')->getManager();

        //je récup tous les trainings pour ensuite récupérer leurs modules avec la méthode getModules()
        $trainings = $em->getRepository(Training::class)->findAll();

        // j'initialise mes variables'
        $totalModules = 0;
        $moduleNames = [];

        // pour chaque training je récupère ses modules 
        foreach ($trainings as $training) {
            foreach ($training->getModules() as $module) {
                // nombre total de modules 
                $totalModules++;
                // Pour chaque module je pousse son nom dans le tableau moduleNames
                $moduleNames[] = $module->getName();
            }
        }

        // requête GET pour accéder à la page qui affiche tous les modules (à coder)
        $crawler = $client->request('GET', '/modules');

        // Je check si j'obtiens bien du 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();

        // check si le nombre de lignes dans le tableau des modules correspond au nombre total de modules
        $this->assertCount($totalModules, $crawler->filter('tbody > tr'));

        // vérifie que chaque nom de module est bien affiché sur la page
        foreach ($moduleNames as $moduleName) {
            // je check chaque nom de module est présent dans la page HTML
            $this->assertStringContainsString($moduleName, $client->getResponse()->getContent());
        }
    }
}


class ModuleDeletionTest extends WebTestCase
{
    // Test pour vérifier que la suppression d'un module fonctionne via la route /deleteModule/{trainingId}/{moduleId}
    public function testDeleteOneModule()
    {

        $client = static::createClient();
        $em = self::$kernel->getContainer()->get('doctrine')->getManager();

        // je recup les ids du training et de son module passé en params de la route
        $trainingId = req.params.trainingId; // ????
        $moduleId = req.params.moduleId; /// ???? j'ai pas la ref en symfony x)

        $training = $em->getRepository(Training::class)->find($trainingId);
        $module = $em->getRepository(Module::class)->find($moduleId);

        // je verifie que le training et le module existent bien en BDD
        $this->assertNotNull($training);
        $this->assertNotNull($module);

        // Envoie une requête GET pour appeler la route de suppression de module via /deleteModule/{trainingId}/{moduleId}
        $client->request('GET', '/deleteModule/' . $trainingId . '/' . $moduleId);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Simule l'appel à la route POST qui gère la suppression réelle
        $client->request('POST', '/deleteModuleAction', [
            'training_id' => $trainingId,
            'module_id' => $moduleId
        ]);

        // je recharge l'état de la BDD pour vérifier la suppression
        $em->clear();
        $trainingReloaded = $em->getRepository(Training::class)->find($trainingId);

        // je check si le module a bien été supprimé du training
        $this->assertFalse($trainingReloaded->getModules()->contains($module));
    }
}


