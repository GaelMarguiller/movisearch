<?php
namespace Controller;

use Doctrine\DBAL\Query\QueryBuilder;

class IndexController
{
    public function indexAction()
    {
        include("search.php");
    }

    public function searchAction()
    {
        //se connecter à la bdd
        header('Content-Type: application/json');

        $conn = \MovieSearch\Connexion::getInstance();
        //creer la requete adéquate

        $title = $_POST['title'];
        $duration = $_POST['duration'];
        $yearStart = $_POST['year_start'];
        $yearEnd = $_POST['year_end'];

        if (isset($title)) {
            $sqlTitle = "SELECT * FROM film WHERE `title` LIKE '$title%'";
        }

        if (isset($duration)) {
            if ($duration == "All") {
                $reqTime = "";
            } else if ($duration == "1") {
                $reqTime = " AND duration < 3600 ";
            } else if ($duration == "2") {
                $reqTime = " AND duration BETWEEN 3600 AND 5400 ";
            } else if ($duration == "3") {
                $reqTime = " AND duration BETWEEN 5400 AND 9000 ";
            } else if ($duration == "4") {
                $reqTime = " AND duration > 9000 ";
            }
        }

        if (isset($yearStart)) {
            $reqYearStart = " AND year >= '$yearStart' ";
        }
        if (empty($yearStart)) {
            $reqYearStart = "";
        }
        if (isset($yearEnd)) {
            $reqYearEnd = " AND year <= '$yearEnd' ";
        }
        if (empty($yearEnd)) {
            $reqYearEnd = "";
        }

        //envoyer la requête à la BDD
        $stmt = $conn->prepare($sqlTitle.$reqTime.$reqYearStart.$reqYearEnd);
        $stmt->execute();
        //renvoyer les films qu'on a trouvés
        $films = $stmt->fetchAll();
        return json_encode(["films" => $films]);
    }
}