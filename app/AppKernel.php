<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),

            new KPhoen\RulerZBundle\KPhoenRulerZBundle(),
            new League\Tactician\Bundle\TacticianBundle(),

            new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle(),
            new Snc\RedisBundle\SncRedisBundle(),

            new KnpU\OAuth2ClientBundle\KnpUOAuth2ClientBundle(),

            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),

            new Regis\AnalysisContext\Infrastructure\Symfony\Bundle\AnalysisBundle\RegisAnalysisBundle(),
            new Regis\GithubContext\Infrastructure\Symfony\Bundle\GithubBundle\RegisGithubBundle(),

            new Regis\Infrastructure\Bundle\AuthBundle\RegisAuthBundle(),
            new Regis\Infrastructure\Bundle\BackendBundle\RegisBackendBundle(),
            new Regis\Infrastructure\Bundle\AppBundle\RegisAppBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
