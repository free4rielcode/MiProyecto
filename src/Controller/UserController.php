<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // or add an optional message - seen by developers
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'User tried to access a page without having ROLE_ADMIN');
        //  $usuarios= $entityManager->getRepository(Usuario::class)->findAll();
        $users = $userRepository->findAll();
        //$usuarios=$usuarioRepository->findBy(array('edad'=>'18'),array('id'=>'asc'));
        //$usuarios=$usuarioRepository->findByName('segundo');
        //$usuarios = $entity_Manager->getRepository(Usuario::class)->findByName('segundo');

        //    //$materias=$entity_Manager->getRepository(Materias::class)->findAll();
        //dump($materias);
        //die();
        //   $ruta = $params->get('app_path_files');

        //    $usuarios = null;
        //$logger->info('Usuarios existentes en BD',['usuarios'=>count($usuarios),'path'=> $request->getRequestUri()]);
        //  if(is_null($usuarios)){
        //     return $this->redirectToRoute('app_usuario_show',);
        //  }

        // return $this->json(['username' => 'jane.doe', 'arreglo de' => ['key1' => 'value1', 'key2' => 'value2']]);
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/api', name: 'app_usuario_api', methods: ['GET'])]
    public function api(
        Request $request,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ParameterBagInterface $params
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'User tried to access a page without having ROLE_ADMIN');
        $logger->notice('Acceso a la API de usuarios', [
            'path' => $request->getRequestUri(),
        ]);

        // Obtiene todos los usuarios desde el repositorio
        $users = $entityManager->getRepository(User::class)->findAll();

        $ruta = $params->get('app_path_files');
        // Log de usuarios existentes
        $logger->info('Usuarios existentes en BD', [
            'usuarios' => count($users),
            'path' => $params->get('app_path_files'),
        ]);

        // Redirige si no hay usuarios
        /* if (is_null($usuarios)) {
            return $this->redirectToRoute('app_usuario_show',['id' => 6]);
        }*/
        // Devuelve una respuesta JSON formateada para visualizar en el navegador los datos de los usuarios
        $encoder = new JsonEncoder();
        $defaulcontext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, ?string $format, array $context): string {
                // serialize the nested Organization with only the name (and not the members)
                return $object->getId();
            },
        ];

        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaulcontext);
        $serializer = new Serializer([$normalizer], [$encoder]);

        $users = $entityManager->getRepository(User::class)->findAll();
        // Devuelve una respuesta JSON
        return new JsonResponse($serializer->serialize($users, 'json'), 200, [], true);
        /* return $this->json([
            'users' => array_map(fn($usuario) => [
                'id' => $usuario->getId(),
                'nombre' => $usuario->getNombre(),
                'email' => $usuario->getEmail(),
                'isVerified' => $usuario->isVerified(),
                'roles' => $usuario->getRoles(),
                'password' => $usuario->getPassword(),
            ], $users),
        ]);*/

        // Renderiza la plantilla Twig (esta línea no se ejecutará debido al return anterior)
        //return $this->render('usuario/index.html.twig', [
        //   'usuarios' => $usuarios,
        //]);
    }

    #[Route('/new', name: 'app_usuario_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form_request = $request->request->all();
            $csrf_token = $form_request['usuario']['_token'];

            if ($this->isCsrfTokenValid('usuario_create' . $user->getId(), $csrf_token)) {
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
            } else {
                echo "El token enviado no es valido, eres un robot, Si no lo eres actualiza la pagina";
            }
        }
        return $this->render('usuario/new.html.twig', [
            'usuario' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
