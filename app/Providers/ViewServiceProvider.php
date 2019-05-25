<?php

namespace App\Providers;

use App\Views\Extensions\DebugExtension;
use App\Views\Extensions\RouteExtension;
use App\Views\View;
use League\Container\ServiceProvider\AbstractServiceProvider;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ViewServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        View::class
    ];

    public function register()
    {
        $container = $this->getContainer();

        $config = $container->get('config');

        $container->share(View::class, function () use ($config, $container) {
            
            $loader = new FilesystemLoader(base_path('resources/views'));

            $twig = new Environment($loader, [
                'cache' => $config->get('twig.cache'),
                'debug' => $config->get('twig.debug')
            ]);

            $twig->addExtension(new RouteExtension(
                $container->get('router'),
                $container->get('request')->getUri()
            ));

            $twig->addExtension(new DebugExtension);

            foreach ($config->get('twig.globals') as $key => $value) {
                $twig->addGlobal($key, $value);
            }

            return new View($twig);

        });
    }
}
