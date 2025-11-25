<?php

namespace App\Controller;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(RouterInterface $router,ParameterBagInterface $params): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
       /* $array = [
            'foo' => 'bar',
            'bar' => ['foo' => 'bar', 'bar' => 'baz'],
        ];

        $yaml = Yaml::dump($array);
        
        file_put_contents('../var/file.yaml', $yaml);
        $value = Yaml::parseFile('../var/file.yaml');
        $path = $params->get('app_path_files');
        dump($params->all());
        die();*/
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
