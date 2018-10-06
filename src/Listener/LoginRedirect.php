<?php
/**
 * Created by PhpStorm.
 * User: Djianni
 * Date: 05/10/2018
 * Time: 10:15
 */

namespace App\Listener;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginRedirect implements AuthenticationSuccessHandlerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @return Response never null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $roles = $token->getRoles();

        $tabRoles = array_map(function($role){
           return $role->getRole();
        },$roles);


        if (\in_array("ROLE_SUPER_ADMIN", $tabRoles, true)){
            $redirection = new RedirectResponse($this->router->generate('admin_index'));
        }
        elseif (\in_array("ROLE_ADMIN", $tabRoles, true)){
            $redirection = new RedirectResponse($this->router->generate('admin_index'));
    }
        elseif (\in_array("ROLE_BANNED", $tabRoles, true)){
            $redirection = new RedirectResponse($this->router->generate('index'));
        }

        else{
            $redirection = new RedirectResponse($this->router->generate('index'));
        }



        return $redirection;
    }
}