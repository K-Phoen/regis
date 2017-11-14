<?php

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Symfony\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AppController extends Controller
{
    public function homeAction()
    {
        return $this->render('@RegisApp/App/home.html.twig');
    }
}
