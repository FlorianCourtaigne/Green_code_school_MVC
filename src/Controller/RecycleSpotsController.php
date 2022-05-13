<?php

namespace App\Controller;

use Symfony\Component\HttpClient\HttpClient;

class RecycleSpotsController extends AbstractController
{
    public function getRecycle()
    {
        $arrondissement = 75020;
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['submit'])) {
            $arrondissement = $_GET["district"];
        }
        $client = HttpClient::create();
        $urlApi = "https://opendata.paris.fr/api/records/1.0/search/?dataset=dechets-menagers-points-dapport-volontaire-stations-trilib&q=&rows=50&facet=date_de_mise_en_service&facet=code_postal&facet=etat&refine.code_postal=" .$arrondissement; // phpcs:ignore
        $response = $client->request('GET', $urlApi);
        $responseAsAnArray = $response->toArray();
        // var_dump($responseAsAnArray['records'][0]);die;
        return $this->twig->render('RecycleSpots/index.html.twig', [
            "spots" => $responseAsAnArray, "arrondissement" => $arrondissement
        ]);
    }
}
