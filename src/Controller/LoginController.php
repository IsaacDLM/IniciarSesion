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
    public function iniciarSesion(Request $request): Response
    {
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $username = trim((string) $form->get('username')->getData());
                $request->getSession()->set('username', $username);
                $this->addFlash('success', sprintf('¡Inicio de sesión correcto, %s!', $username));
                $response = $this->redirectToRoute('app_welcome');
            } else {
                $this->addFlash('error', 'No se pudo iniciar sesión. Revisa el nombre de usuario.');
                $response = $this->render('login/index.html.twig', [
                    'form' => $form,
                ]);
            }
        } else {
            $response = $this->render('login/index.html.twig', [
                'form' => $form,
            ]);
        }
        return $response;
    }

    #[Route('/bienvenida', name: 'app_welcome', methods: ['GET'])]
    public function bienvenida(Request $request): Response
    {
        $username = $request->getSession()->get('username');

        if (!is_string($username) || '' === $username) {
            $response = $this->redirectToRoute('app_login');
        } else {
            $response = $this->render('login/welcome.html.twig', [
                'username' => $username,
            ]);
        }
        return $response;
    }

    #[Route('/cerrar-sesion', name: 'app_logout', methods: ['POST'])]
    public function cerrarSesion(Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('logout', $request->request->getString('_token')))) {
            $this->addFlash('error', 'No se pudo cerrar sesión. Vuelve a intentarlo.');
            $response = $this->redirectToRoute('app_welcome');
        } else {
            $request->getSession()->remove('username');
            $this->addFlash('success', 'Has cerrado sesión correctamente.');
            $response = $this->redirectToRoute('app_login');
        }
        return $response;
    }
}
