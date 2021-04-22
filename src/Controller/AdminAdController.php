<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\IdeeType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads", name="admin_ads_index")
     */
    public function index(AdRepository $repo)
    {
        return $this->render('admin/ad/index.html.twig', [
            'ads'=>$repo->findAll()
        ]);
    }

    /**
     * Permet de modifier un projet par le formulaire d'édition par l'administration
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     *
     * @param Ad $ad
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(IdeeType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le projet <strong>{$ad->getTitle()}</strong> a bien été modifié !"

            );
        }

        return $this->render('admin/ad/edit.html.twig',[
            'ad'=>$ad,
            'form'=>$form->createView()
        ]);

    }

    /**
     * Permet de supprimer un projet
     * @Route("admin/ads/{id}/delete", name="admin_ads_delete")
     * 
     *
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $manager)
    {
        if(count($ad->getBookings()) > 0)
        {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer le projet :<strong> {$ad->getTitle() } </strong> car il a déjà une réservation "
            );
        }
        else
        {
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le projet <strong> {$ad->getTitle()} </strong> a bien été supprimé !"
            );
        }
        
       return $this->redirectToRoute('admin_ads_index');
    }
}
