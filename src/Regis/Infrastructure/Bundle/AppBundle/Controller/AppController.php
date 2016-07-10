<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Regis\Application\Command;
use Regis\Domain\Entity;

class AppController extends Controller
{
    public function homeAction()
    {
        return $this->render('@RegisApp/App/home.html.twig');
    }
}
