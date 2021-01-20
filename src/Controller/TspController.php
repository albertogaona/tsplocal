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
     * @Route("/{id}")
     * @param Request $request
     * @param string $id
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request, $id = "-1", EntityManagerInterface $entityManager, LoggerInterface $logger)
    {

        $cn = $entityManager->getConnection();

        $logger->error("ENTRA:::::::");

        $date = $cn->executeQuery("SELECT UTC_TIMESTAMP() ")->fetchColumn();

        return $this->json([
            "code" => Response::HTTP_OK,
            "data" => [
                "id" => $id,
                "date" => $date,
            ],
            "error" => false
        ]);
    }
}