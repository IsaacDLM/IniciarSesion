<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class LoginController extends AbstractController
{
    #[Route('/', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $username = trim((string) $form->get('username')->getData());
                $request->getSession()->set('username', $username);
                $this->addFlash('success', sprintf('¡Inicio de sesión correcto, %s!', $username));

                return $this->redirectToRoute('app_welcome');
            }

            $this->addFlash('error', 'No se pudo iniciar sesión. Revisa el nombre de usuario.');
        }

        return $this->render('login/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/bienvenida', name: 'app_welcome', methods: ['GET'])]
    public function welcome(Request $request): Response
    {
        $username = $request->getSession()->get('username');

        if (!is_string($username) || '' === $username) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('login/welcome.html.twig', [
            'username' => $username,
        ]);
    }

    #[Route('/cerrar-sesion', name: 'app_logout', methods: ['POST'])]
    public function logout(Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('logout', $request->request->getString('_token')))) {
            $this->addFlash('error', 'No se pudo cerrar sesión. Vuelve a intentarlo.');

            return $this->redirectToRoute('app_welcome');
        }

        $request->getSession()->remove('username');
        $this->addFlash('success', 'Has cerrado sesión correctamente.');

        return $this->redirectToRoute('app_login');
    }
}
