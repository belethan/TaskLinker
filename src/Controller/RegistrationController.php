<?php
namespace App\Controller;

use App\Entity\Employe;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $employe = new Employe();
        $form = $this->createForm(RegistrationType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode le mot de passe
            $plainPassword = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($employe, $plainPassword);
            $employe->setPassword($hashedPassword);

            // Rôle par défaut
            $employe->setRoles(['ROLE_EMPLOYE']);

            $em->persist($employe);

            if ($form->get('password')->getData() !== $form->get('confirmPassword')->getData()) {
                $this->addFlash('danger', 'Les mots de passe ne correspondent pas.');
                return $this->render('identification/register.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
            $em->flush();

            $this->addFlash('success', 'Inscription réussie, vous pouvez vous connecter !');

            return $this->redirectToRoute('login');
        }

        return $this->render('identification/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
