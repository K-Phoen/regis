<?php

declare(strict_types=1);

namespace Regis\Kernel\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthController extends Controller
{
    public function loginAction()
    {
        return $this->render('@RegisApp/Auth/login.html.twig');
    }
}
