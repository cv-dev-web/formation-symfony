<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/bonjour/{prenom}", name="hello_prenom")
     * @Route ("/hello", name="hello_base")
     * @route ("/bonjour/{prenom}/age/{age}", name="hello")
     * Montre la page de bonjour
     *
     * @return void
     */
    public function hello($prenom = "anonyme", $age = 0)
    {
        /* return new Response("Bonjour " . $prenom. " tu as ". $age . " ans."); */
        return $this->render(
            'hello.html.twig',
            [
                'prenom' => $prenom,
                'age' => $age

            ]

            );
    }
    /**
     * @Route("/", name="homepage")
     */
    public function home()
    {
        /* return new Response("
        <html>
            <head>
                <title>Ma première application Symfony</title>
            </head>
            <body>
                <h1>Bonjour à tous</h1>
                <p> Mom premier paragraphe Symfony</p>
            </body>
        </html>"); */

        $prenoms = ["Gabrielle"=>5,"Ayden"=>25,'Cassiopée'=>15];

        return $this->render(
            'home.html.twig',

            [
                'title'=>"Bonjour une nouvelle fois",
                'age'=> 15,
                'tableau'=> $prenoms
            ]
        );
    }
}

?>