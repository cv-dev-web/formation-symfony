<?php

namespace App\Controller;



use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Service\PaginationService;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * Page d'admisnistration des reservations
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_booking_index")
     * 
     * @param BookingRepository $repo
     * 
     * @return Response
     */
    public function index(BookingRepository $repo, $page,PaginationService $pagination)
    {

        $pagination->setEntityClass(Booking::class)
                   ->setPage($page); 

        return $this->render('admin/booking/index.html.twig', [
            'pagination'=> $pagination
        ]);
    }


    /**
     * permet de modifier une reservation
     * @Route("/admin/booking/{id}/edit", name="admin_booking_edit")
     * 
     *
     * @return Response
     */
    public function edit(Booking $booking,Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdminBookingType::class, $booking,[
            'validation_groups'=>["default"]
        ]);


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $booking->setAmount(0);
            
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La reservation n°{$booking->getId()} a bien été modifié !"
            );

            return $this->redirectToRoute("admin_booking_index");
        }

        return $this->render('admin/booking/edit.html.twig',[
            'booking'=> $booking,
            'form'=> $form->createView()
        ]);
    }


    /**
     * permet de supprimer une reservation
     * @Route("/admin/bookings/{id}/delete" , name="admin_booking_delete")
     *
     * @return Response
     */
    public function delete(Booking $booking, EntityManagerInterface $manager)
    {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "La reservation a bien était supprimée !"
        );

        return $this->redirectToRoute('admin_booking_index');
    }
}
