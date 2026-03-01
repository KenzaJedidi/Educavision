<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private RouterInterface $router)
    {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        // Rediriger vers la page demandée si l'utilisateur tentait d'y accéder (ex: Mes réclamations)
        $targetPath = $request->getSession()->get('_security.main.target_path');
        if ($targetPath) {
            $request->getSession()->remove('_security.main.target_path');
            return new RedirectResponse($targetPath);
        }

        $roles = $token->getRoleNames();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            return new RedirectResponse($this->router->generate('admin_dashboard'));
        }

        if (in_array('ROLE_PROF', $roles, true)) {
            return new RedirectResponse($this->router->generate('teacher_dashboard'));
        }

        // Étudiant ou ROLE_USER → interface étudiant (liste des cours)
        return new RedirectResponse($this->router->generate('front_cours_list'));
    }
}
