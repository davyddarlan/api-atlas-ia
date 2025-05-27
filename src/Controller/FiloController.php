<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ValidatorManager;
use App\Entity\Filo;

/**
 * @Route("/api/especie/cladograma/filo", name="especie_", format="json")
 */
class FiloController extends AbstractController
{
    private $validatorManager;
    
    public function __construct(ValidatorManager $validatorManager) 
    {
        $this->validatorManager = $validatorManager;
    }

    /**
     * @Route("/criar-filo", name="criar_filo", methods="POST")
     */
    public function criarFilo(Request $request): Response
    {  
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();

        $input = [
            'nome' => empty($data['nome']) ? '' : $data['nome'],
        ];

        $entity = new Filo;

        $entity->setNome($input['nome']);

        $errors = $this->validatorManager->validate($entity);

        if ($this->validatorManager->hasError($errors)) {
            return $this->validatorManager->response();
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($entity);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $entity->getId(),
            'nome' => $entity->getNome(),
        ]);
    }

    /**
     * @Route("/buscar-filo/{nome}", name="buscar_filo", methods="GET")
     */
    public function buscarFilo(Request $request, $nome = ''): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if (empty($nome)) {
            return new JsonResponse([]);
        }
        
        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->createQuery(
            'SELECT filo 
            FROM App\Entity\Filo filo
            WHERE filo.nome 
            LIKE :nome'
        )->setParameter('nome', $nome . '%');

        if (!count($query->getResult())) {
            return new JsonResponse([]);
        }

        foreach ($query->getResult() as $entity) {
            $entities[] = [
                'id' => $entity->getId(),
                'nome' => $entity->getNome(),
            ];
        }

        return new JsonResponse($entities);
    }
}