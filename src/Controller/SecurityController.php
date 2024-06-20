<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;


class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_acceuil');
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        if ($error instanceof BadCredentialsException) {
            $errorMessage = "Adresse email ou mot de passe incorrect.";
        } else {
            $errorMessage = null;
        }
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error_message' => $errorMessage,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route(path: '/register', name: 'app_register', methods: ['GET', 'POST'])]
public function register(
    Request $request,
    UserPasswordHasherInterface $passwordHasher,
    UserRepository $userRepository,
    EntityManagerInterface $entityManager
): Response {
    $error = '';
    if ($request->isMethod('GET')) {
        
        return $this->render('security/register.html.twig', ['error' => $error]);
    }
    
    $user = new User();

    // Get form data
    $mail = $request->request->get('mail');
    $username = $request->request->get('username');
    $plainPassword = $request->request->get('password');
    $confirmPassword = $request->request->get('confirm_password');




    // Basic validation
    if (empty($mail) || empty($username) || empty($plainPassword)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($plainPassword) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($userRepository->findOneBy(['mail' => $mail])) {
        $error = 'Email is already registered.';
    } elseif ($userRepository->findOneBy(['username' => $username])) {
        $error = 'Username is already taken.';
    } else if ($plainPassword !== $confirmPassword) {
        $error = 'Passwords do not match.';
    }

    if ($error) {
        return $this->render('security/register.html.twig', ['error' => $error]);
    }

    // Hash password
    $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

    // Set user data
    $user->setUsername($username)
         ->setPassword($hashedPassword)
         ->setRole('ROLE_USER')
         ->setMail($mail);

    // Save user
    $entityManager->persist($user);
    $entityManager->flush();

    return $this->redirectToRoute('app_login');
}
}
