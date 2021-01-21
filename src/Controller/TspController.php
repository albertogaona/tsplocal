<?php


namespace App\Controller;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/tsp")
 */
class TspController extends AbstractController
{


    /**
     * @Route("/getInfo", name="tsp_getInfo")
     * @param Request $request
     * @param string $id
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request, $id = "-1",
                          EntityManagerInterface $entityManager,
                          LoggerInterface $logger)
    {

        $cn = $entityManager->getConnection();

        $logger->error("ENTRA:::::::");

        $date = $cn->executeQuery("SELECT UTC_TIMESTAMP() ")->fetchOne();
        $pines = $cn->executeQuery("SELECT * FROM pines")->fetchAllAssociative();

        //$date = gmdate('Y:m:d H:i:s');

        $freeSpace = self::freeSpaceHumanReadable(disk_free_space("/"));
        $totalSpace = self::freeSpaceHumanReadable(disk_total_space("/"));

        return $this->json([
            "code" => Response::HTTP_OK,
            "data" => [
                "id" => $id,
                "date" => $date,
                "pines" => $pines,
               "hardware" => [
                   "free_space" => $freeSpace,
                   "total_space" => $totalSpace,
               ]
            ],
            "error" => false
        ]);
    }

    private function freeSpaceHumanReadable($space)
    {
        $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
        $base = 1024;
        $class = min((int)log($space , $base) , count($si_prefix) - 1);
        return sprintf('%1.2f' , $space / pow($base, $class)) . ' ' . $si_prefix[$class];
    }
}