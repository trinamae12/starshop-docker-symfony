<?php

namespace App\Controller;

use App\Entity\Starship;
use App\Model\StarshipStatusEnum;
use App\Repository\StarshipRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/starships')]
class StarshipApiController extends AbstractController
{
    private $repository;
    private $serializer;
    public function __construct(StarshipRepository $repository, SerializerInterface $serializer)
    {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    // #[Route('', methods: ['GET'])]
    public function getCollection(StarshipRepository $repository): JsonResponse
    {
        $starships = $repository->findAllStarships();
        $data = $this->serializer->serialize($starships, 'json');

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
        // return $this->json($data);
    }

    #[Route('', methods: ['GET'])]
    public function getPaginatedStarships(Request $request): JsonResponse
    {
        $page = max($request->query->getInt('page', 1), 1);
        $limit = max($request->query->getInt('limit', 5), 1);

        $paginator = $this->repository->findPaginatedStarships($page, $limit);

        $totalItems = count($paginator);
        $totalPages = ceil($totalItems/$limit);

        $data = [];
        foreach($paginator as $starship) {
            $data[] = [
                'id' => $starship->getId(),
                'name' => $starship->getName(),
                'class' => $starship->getClass(),
                'captain' => $starship->getCaptain(),
                'status' => $starship->getStatus(),
            ];
        }

        return new JsonResponse([
            'data' => $data,
            'meta' => [
                'totalItems' => $totalItems,
                'itemsPerPage' => $limit,
                'totalPages' => $totalPages,
                'currentPage' => $page
            ]
        ], JsonResponse::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('', methods: ['POST'])]
    public function add (Request $request): Response 
    {
        $statuses = StarshipStatusEnum::cases();
        $matchingStatusIndex = array_search($request->get('status'), array_column($statuses, "value"));

        if($matchingStatusIndex === false) {
            throw new BadRequestException("Status not valid");
        }

        $starship = new Starship();
        $starship->setName($request->get('name'));
        $starship->setCaptain($request->get('captain'));
        $starship->setClass($request->get('class'));
        $starship->setStatus($request->get('status'));

        $this->repository->createStarship($starship);

        return $this->json($starship);
    }

    #[Route('/{id<\d+>}', methods: ['GET'])]
    public function get(int $id, StarshipRepository $repository): JsonResponse
    {   
        $starship = $repository->findStarship($id);

        if(!$starship) {
            throw $this->createNotFoundException('Starship not found!');
        }

        $data = $this->serializer->serialize($starship, 'json');

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/{id<\d+>}', methods: ['PUT'])]
    public function update(Request $request, int $id): Response
    {   
        $starship = $this->repository->findStarship($id);

        if (!$starship) {
            throw new NotFoundHttpException('Starship not found');
        }

        $starship->setName($request->get('name'));
        $starship->setCaptain($request->get('captain'));
        $starship->setClass($request->get('class'));
        $starship->setStatus($request->get('status'));

        $this->repository->updateStarship($starship);

        return $this->json($starship);
    }

    #[Route('/{id<\d+>}', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $starship = $this->repository->findStarship($id);

        if (!$starship) {
            return new Response('User not found!', Response::HTTP_NOT_FOUND);
        }

        $this->repository->deleteStarship($starship);

        return new Response('Starship successfully deleted', Response::HTTP_OK);
    }
}
