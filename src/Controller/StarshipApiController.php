<?php

namespace App\Controller;

use App\Entity\Starship;
use App\Model\StarshipStatusEnum;
use App\Repository\StarshipRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/starships')]
class StarshipApiController extends AbstractController
{
    private $repository;
    public function __construct(StarshipRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('', methods: ['GET'])]
    public function getCollection(StarshipRepository $repository): Response
    {
        $starships = $repository->findAllStarships();

        return $this->json($starships);
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
    public function get(int $id, StarshipRepository $repository): Response
    {   
        $starship = $repository->findStarship($id);

        if(!$starship) {
            throw $this->createNotFoundException('Starship not found!');
        }

        return $this->json($starship);
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
