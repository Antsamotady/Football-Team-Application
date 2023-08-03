<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerSearchType;
use App\Form\PlayerType;
use App\Repository\TeamRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/player')]
class PlayerController extends AbstractController
{
    #[Route('/', name: 'player_index', methods: ['GET', 'POST'])]
    public function index(Request $request, PlayerRepository $playerRepository): Response
    {
        $playerSearchForm = $this->createForm(PlayerSearchType::class);
        $playerSearchForm->handleRequest($request);

        $searchData = $playerSearchForm->getData();
        $players = $playerRepository->findBySearchData($searchData);

        return $this->render('player/index.html.twig', [
            'players' => $players,
            'player_search_form' => $playerSearchForm->createView()
        ]);
    }

    #[Route('/new', name: 'player_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $player = new Player();
        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($player);
            $entityManager->flush();

            return $this->redirectToRoute('player_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('player/new.html.twig', [
            'player' => $player,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'player_show', methods: ['GET'])]
    public function show(Player $player): Response
    {
        return $this->render('player/show.html.twig', [
            'player' => $player,
        ]);
    }

    #[Route('/{id}/edit', name: 'player_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Player $player, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('player_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('player/edit.html.twig', [
            'player' => $player,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'player_delete', methods: ['POST'])]
    public function delete(Request $request, Player $player, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$player->getId(), $request->request->get('_token'))) {
            $entityManager->remove($player);
            $entityManager->flush();
        }

        return $this->redirectToRoute('player_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/get-team-players/{teamId}', name: 'get_team_players', methods: ['GET'])]
    public function getPlayerData(TeamRepository $teamRepository, PlayerRepository $playerRepository, Request $request, $teamId): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new \RuntimeException('Invalid request');
        }

        $team = $teamRepository->find($teamId);
        $players = $playerRepository->findBy(['team' => $teamId], ['name' => 'ASC']);
        $nonPlayers = $playerRepository->findPlayersAvailableForSale($teamId);


        return $this->render('player/available_player_for_sale.html.twig', [
            'nbPlayer' => count($players),
            'nbNonPlayer' => count($nonPlayers),
            'team' => $team,
            'players' => $players,
            'nonPlayers' => $nonPlayers 
        ]);
    }
}
