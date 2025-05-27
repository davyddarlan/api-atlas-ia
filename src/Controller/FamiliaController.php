<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ValidatorManager;
use App\Entity\Familia;

/**
 * @Route("/api/especie/cladograma/familia", name="especie_", format="json")
 */
class FamiliaController extends AbstractController
{
    private $validatorManager;
    
    public function __construct(ValidatorManager $validatorManager) 
    {
        $this->validatorManager = $validatorManager;
    }

    /**
     * @Route("/criar-familia", name="criar_familia", methods="POST")
     */
    public function criarFamilia(Request $request): Response
    {  
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();

        $input = [
            'nome' => empty($data['nome']) ? '' : $data['nome'],
        ];

        $entity = new Familia;

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
     * @Route("/buscar-familia/{nome}", name="buscar_familia", methods="GET")
     */
    public function buscarFamilia(Request $request, $nome = ''): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if (empty($nome)) {
            return new JsonResponse([]);
        }
        
        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->createQuery(
            'SELECT familia 
            FROM App\Entity\Familia familia
            WHERE familia.nome 
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