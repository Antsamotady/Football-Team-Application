<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/team')]
class TeamController extends AbstractController
{
    /**
     *
     * @var TeamRepository
     */
    private $teamRepository;

    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    #[Route('/', name: 'team_index', methods: ['GET'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $teams = $paginator->paginate(
            $this->teamRepository->findAllByQuery(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            1 /*limit per page*/
        );

        return $this->render('team/index.html.twig', [
            'teams' => $teams
        ]);
    }

    #[Route('/new', name: 'team_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedPlayers = $form->get('player')->getData();

            foreach ($selectedPlayers as $player) {
                $player->setTeam($team);
                $entityManager->persist($player);
            }

            $entityManager->persist($team);
            $entityManager->flush();

            $this->addFlash('success', 'New team created succesfully.');
            return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('team/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/deal', name: 'team_deal', methods: ['GET'])]
    public function deal(Request $request): Response
    {
        $teams = $this->teamRepository->findAll();

        return $this->render('team/deal.html.twig', [
            'teams' => $teams
        ]);
    }

    #[Route('/player/sell/{teamId}/{playerId}', name: 'player_sell', methods: ['GET'])]
    public function sellPlayer($teamId, $playerId, EntityManagerInterface $entityManager, TeamRepository $teamRepository, PlayerRepository $playerRepository): Response
    {
        $team = $teamRepository->find($teamId);
        $player = $playerRepository->find($playerId);

        $player->setIsAvailableForSale(true);
        
        $entityManager->flush();

        $this->addFlash('success', 'Transaction completed succesfully.');
        return $this->redirectToRoute('team_deal');
    }

    #[Route('/player/buy/{teamId}/{playerId}', name: 'player_buy', methods: ['GET'])]
    public function buyPlayer($teamId, $playerId, EntityManagerInterface $entityManager, TeamRepository $teamRepository, PlayerRepository $playerRepository): Response
    {
        $team = $teamRepository->find($teamId);
        $player = $playerRepository->find($playerId);
        $ancientTeamOwner = $player->getTeam();

        if (($team->getMoney() - $player->getPrice()) < 0) {
            $this->addFlash('error', 'Aborted because this team doesn\' possess enough balance!');

            return $this->redirectToRoute('team_deal');
        }

        $ancientTeamOwner->setMoney($ancientTeamOwner->getMoney() + $player->getPrice());
        $team->setMoney($team->getMoney() - $player->getPrice());
        $player->setTeam($team);
        $player->setIsAvailableForSale(false);
        
        $entityManager->persist($ancientTeamOwner);
        $entityManager->flush();

        $this->addFlash('success', 'Transaction completed succesfully.');
        return $this->redirectToRoute('team_deal');
    }


    #[Route('/{id}', name: 'team_show', methods: ['GET'])]
    public function show(Team $team): Response
    {
        return $this->render('team/show.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route('/{id}/edit', name: 'team_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Team $team, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('team/edit.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'team_delete', methods: ['POST'])]
    public function delete(Request $request, Team $team, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $entityManager->remove($team);
            $entityManager->flush();
        }

        return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
