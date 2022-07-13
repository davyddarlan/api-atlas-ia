<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ValidatorManager;
use App\Entity\Divisao;

/**
 * @Route("/api/especie/cladograma/divisao", name="especie_", format="json")
 */
class DivisaoController extends AbstractController
{
    private $validatorManager;
    
    public function __construct(ValidatorManager $validatorManager) 
    {
        $this->validatorManager = $validatorManager;
    }

    /**
     * @Route("/criar-divisao", name="criar_divisao", methods="POST")
     */
    public function criarDivisao(Request $request): Response
    {  
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();

        $input = [
            'nome' => empty($data['nome']) ? '' : $data['nome'],
        ];

        $entity = new Divisao;

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
     * @Route("/buscar-divisao/{nome}", name="buscar_divisao", methods="GET")
     */
    public function buscarDivisao(Request $request, $nome = ''): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if (empty($nome)) {
            return new JsonResponse([]);
        }
        
        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->createQuery(
            'SELECT divisao 
            FROM App\Entity\Divisao divisao
            WHERE divisao.nome 
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