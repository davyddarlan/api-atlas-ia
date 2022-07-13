<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ValidatorManager;
use App\Entity\SubClasse;

/**
 * @Route("/api/especie/cladograma/subclasse", name="especie_", format="json")
 */
class SubclasseController extends AbstractController
{
    private $validatorManager;
    
    public function __construct(ValidatorManager $validatorManager) 
    {
        $this->validatorManager = $validatorManager;
    }

    /**
     * @Route("/criar-subclasse", name="criar_subclasse", methods="POST")
     */
    public function criarSubclasse(Request $request): Response
    {  
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = $request->request->all();

        $input = [
            'nome' => empty($data['nome']) ? '' : $data['nome'],
        ];

        $entity = new SubClasse;

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
     * @Route("/buscar-subclasse/{nome}", name="buscar_subclasse", methods="GET")
     */
    public function buscarSubclasse(Request $request, $nome = ''): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if (empty($nome)) {
            return new JsonResponse([]);
        }
        
        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->createQuery(
            'SELECT subclasse 
            FROM App\Entity\SubClasse subclasse
            WHERE subclasse.nome 
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