<?php

declare(strict_types=1);

namespace Regis\Kernel\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AppController extends Controller
{
    public function homeAction()
    {
        return $this->render('@RegisApp/App/home.html.twig');
    }
}
