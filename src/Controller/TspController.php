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
    public function index(Request $request,
                          EntityManagerInterface $entityManager,
                          LoggerInterface $logger)
    {

        $cn = $entityManager->getConnection();

        $logger->error("ENTRA:::::::");

        $date = $cn->executeQuery("SELECT UTC_TIMESTAMP() ")->fetchOne();
        $accountNumber = $cn->executeQuery("SELECT account_number, armed_state FROM site_status LIMIT 1")
            ->fetchAllAssociative();
        $pines = $cn->executeQuery("SELECT * FROM pines")->fetchAllAssociative();

        //$date = gmdate('Y:m:d H:i:s');

        $freeSpace = self::freeSpaceHumanReadable(disk_free_space("/"));
        $totalSpace = self::freeSpaceHumanReadable(disk_total_space("/"));

        return $this->json([
            "code" => Response::HTTP_OK,
            "data" => [
                "date" => $date,
                "pines" => $pines,
                "site" => $accountNumber,
                "hardware" => [
                    "free_space" => $freeSpace,
                    "total_space" => $totalSpace,
                ]
            ],
            "error" => false
        ]);
    }

    /**
     * @Route("/getInfo/{pin}", name="tsp_getInfo_pin")
     * @param Request $request
     * @param string $id
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @throws Exception
     */
    public function getInfoByPin(Request $request, $pin = "-1",
                          EntityManagerInterface $entityManager,
                          LoggerInterface $logger)
    {

        $cn = $entityManager->getConnection();

        $logger->error("ENTRA:::::::");

        $date = $cn->executeQuery("SELECT UTC_TIMESTAMP() ")->fetchOne();
        $accountNumber = $cn->executeQuery("SELECT account_number, armed_state FROM site_status LIMIT 1")
            ->fetchAllAssociative();
        $pines = $cn->executeQuery("SELECT * FROM pines WHERE :pin ", ["pin" => $pin])->fetchAllAssociative();

        //$date = gmdate('Y:m:d H:i:s');

        $freeSpace = self::freeSpaceHumanReadable(disk_free_space("/"));
        $totalSpace = self::freeSpaceHumanReadable(disk_total_space("/"));

        var_dump($accountNumber);
        var_dump($accountNumber[0]);

        $retArr = [
            "code" => Response::HTTP_OK,
            "data" => [
                "date" => $date,
                "pines" => $pines,
                "site" => count($accountNumber)>0 ? $accountNumber[0]
                    : array("account_number" => null, "armed_state" => null),
                "hardware" => [
                    "free_space" => $freeSpace,
                    "total_space" => $totalSpace,
                ]
            ],
            "error" => false
        ];

        var_dump($retArr);

        $retJson = $this->json($retArr);

        var_dump($retJson);

        return $retJson;
    }
    private function freeSpaceHumanReadable($space)
    {
        $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
        $base = 1024;
        $class = min((int)log($space , $base) , count($si_prefix) - 1);
        return sprintf('%1.2f' , $space / pow($base, $class)) . ' ' . $si_prefix[$class];
    }
}