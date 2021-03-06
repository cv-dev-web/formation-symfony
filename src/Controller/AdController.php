<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\IdeeType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo) 
    {
        //$repo = $this->getDoctrine()->getRepository(Ad::class);

        $ads = $repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
        ]);
    }
    /**
     * permet de créér une invention
     * 
     * @Route("ads/new", name="ads_create")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $ad = new Ad();

        
        $form = $this->createForm(IdeeType::class, $ad);

        $form->handleRequest($request);
        /* $this->addFlash(
            'success',
            "Le deuxième <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
        );
        $this->addFlash(
            'danger',
            "Message erreur <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
        ); */
        if($form->isSubmitted()&& $form->isValid())
        {
            //$manager = $this->getDoctrine()->getManager();

            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());
            
            $manager->persist($ad);
            $manager->flush();
            
            $this->addFlash(
                'success',
                "Le projet <strong>{$ad->getTitle()}Test</strong> a bien été enregistrée !"
            );

            return $this->redirectToRoute('ads_show',[
                'slug'=> $ad->getSlug()
            ]);
        }
        
        return $this->render(
            'ad/new.html.twig', [
                'form'=>$form->createView()
            ]
        );
    }
    /**
     * Permet d'afficher le formulaire d'edition
     * 
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()",
     * message="Cette annonce ne vous appartient pas, vous n'en êtes pas l'auteur, vous ne pouvez pas la modifier")
     * 
     *
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(IdeeType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid())
        {
            //$manager = $this->getDoctrine()->getManager();

            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }
            
            $manager->persist($ad);
            $manager->flush();
            
            $this->addFlash(
                'success',
                "Le projet <strong>{$ad->getTitle()}Test</strong> a bien été Modifié !"
            );

            return $this->redirectToRoute('ads_show',[
                'slug'=> $ad->getSlug()
            ]);
        }

        return $this->render('ad/edit.html.twig',[
            'form'=> $form->createView(),
            'ad'=>$ad

    ]);
    }

    /**
     * Permet d'afficher une seule inventions
     * @Route("/ads/{slug}", name="ads_show")
     *
     * @return Response
     */
    public function show(/*$slug, AdRepository $repo*/Ad $ad)
    {
        //je recupère l'annonce uniquement correpondant au slug
       // $ad = $repo->findOneBySlug($slug);
        return $this->render(
            'ad/show.html.twig',
            [
                'ad' => $ad
            ]
           
        );
    }

    /**
     * Permet de supprimer une invention
     *@Route("/ads/{slug}/delete", name="ads_delete")
     *@Security("is_granted('ROLE_USER') and user == ad.getAuthor()",
     *message="Vous n'avez pas le droit d'acceder à cette ressource") 
     * 
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $manager)
    {
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'invention <strong> {$ad->getTitle()} </strong> a bien été supprimée !"
        );

        return $this->redirectToRoute("ads_index");

    }
    
}
