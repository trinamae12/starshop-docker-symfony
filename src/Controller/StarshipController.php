<?php

namespace App\Controller;

use App\Entity\Starship;
use App\Model\StarshipStatusEnum;
use App\Repository\StarshipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as AnnotationRoute;
use Symfony\Component\Routing\Attribute\Route;

class StarshipController extends AbstractController
{   
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    #[Route('/starships', name: 'create_starship')]
    public function add(): Response
    {
        $starship = new Starship();
        $starship->setName('test starship');
        $starship->setCaptain('test capatin');
        $starship->setClass('test class');
        $starship->setStatus(StarshipStatusEnum::IN_PROGRESS->value);

        $this->entityManager->persist($starship);
        $this->entityManager->flush();

        return $this->json($starship);

        //return $this->redirectToRoute('app_main_homepage');

    }
    // Name is from ./bin/console debug:router
    #[Route('/starships/{id<\d+>}', name: 'app_starship_show')]
    public function show(int $id, StarshipRepository $repository): Response
    {
        $ship = $repository->find($id);
        
        if(!$ship) {
            throw $this->createNotFoundException('Starship not found!');
        }

        return $this->render('starship/show.html.twig',[
            'ship' => $ship,
        ]);
    }
}