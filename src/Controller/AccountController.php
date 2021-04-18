<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gerer le formulaire de connexion'
     * 
     * @Route("/login", name="account_login")
     *
     * @return Response 
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        
        return $this->render('account/login.html.twig', [
            'hasError' =>$error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se déconnecter
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout()
    { 
        //rien.....
    }

    /**
     * Permet d'afficher le formulaire d'inscription
     * @Route("/register", name="account_register" )
     *
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);


            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été créé, vous pouvez maintenant vous connecter !"
            );

            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * permet d'afficher et de traiter le formulaire de modification de profil
     * 
     * @Route("/account/profile", name="account_profile")
     *
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données du profil ont bien été enregistrées avec succès."
            );

        }
        return $this->render('account/profile.html.twig',[
            'form'=> $form->createView()
        ]);
    }
    /**
     * permet dde modifier le mot de passe
     * 
     * @Route("/account/update-password", name="account_password")
     *
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        $passwordUpdate = new PasswordUpdate();

        $user= $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //1. Verifier que le oldPassword du formulaire soit le même que le password de l'utilisateur
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash()))
            {
                //Gerer l'érreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe est incorrect !"));
            }
            else
            {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                'success',
                "Le mot de passe à bien été modifié avec succès."
                );

                return $this->redirectToRoute('homepage');
            }
            //password_verify('password','$2y$13$veuDR.CA1riouBkk5559/uzy4/5mGUfAYKONzqmGbzkZPlMnoy3Iu');
            

            
        }

        return $this->render('account/password.html.twig',[
            'form'=> $form->createView()
        ]);
    }

        /**
         * Permet d'afficher le profil de l'utilisateur connecté
         * 
         * @Route("/account", name="account_index")
         *
         * @return Response
         */
    public function myAccount()
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}
