<?php

namespace App\Controller;

use Symfony\Component\HttpClient\HttpClient;

// $arrondissements = [75001, 75002, 75003, 75004, 75005, 75006, 75007,
// 75008, 75009, 75010, 75011, 75012, 75013, 75014, 75015, 75016, 75017, 75018, 75019, 75020];


class RecycleSpotsController extends AbstractController
{
    public function get()
    {
        $arrondissements = ['75012'];
        $client = HttpClient::create();
        $urlApi = "https://opendata.paris.fr/api/records/1.0/search/?dataset=dechets-menagers-points-dapport-volontaire-stations-trilib&q=&rows=50&facet=date_de_mise_en_service&facet=code_postal&facet=etat&refine.code_postal=75020"; // phpcs:ignore
        $response = $client->request('GET', $urlApi);
        $responseAsAnArray = $response->toArray();
        // var_dump($responseAsAnArray['records'][0]);die;
        return $this->twig->render('RecycleSpots/index.html.twig', [
            "spots" => $responseAsAnArray, "arrondissements" => $arrondissements
        ]);
    }
}
