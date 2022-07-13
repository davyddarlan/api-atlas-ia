<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Especie;
use App\Entity\Descobridor;
use App\Service\ValidatorManager;

/**
 * @Route("/api/especie/descobridor", name="especie_", format="json")
 */
class DescobridorController extends AbstractController
{
    private $validatorManager;
    
    public function __construct(ValidatorManager $validatorManager)
    {
        $this->validatorManager = $validatorManager;
    }
    
    /**
     * @Route("/criar-descobridor", name="criar_descobridor", methods="POST")
     */
    public function criarDescobridor(Request $request): Response
    {  
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();

        $input = [
            'nome' => empty($data['nome']) ? '' : $data['nome'],
        ];

        $descobridor = new Descobridor;

        $descobridor->setNome($input['nome']);

        $errors = $this->validatorManager->validate($descobridor);

        if ($this->validatorManager->hasError($errors)) {
            return $this->validatorManager->response();
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($descobridor);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $descobridor->getId(),
            'nome' => $descobridor->getNome(),
        ]);
    }

    /**
     * @Route("/buscar-descobridor/{nome}", name="buscar_descobridor", methods="GET")
     */
    public function buscarDescobridor(Request $request, $nome = ''): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if (empty($nome)) {
            return new JsonResponse([]);
        }
        
        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->createQuery(
            'SELECT descobridor 
            FROM App\Entity\Descobridor descobridor
            WHERE descobridor.nome 
            LIKE :nome'
        )->setParameter('nome', $nome . '%');

        if (!count($query->getResult())) {
            return new JsonResponse([]);
        }

        foreach ($query->getResult() as $descobridor) {
            $descobridores[] = [
                'id' => $descobridor->getId(),
                'nome' => $descobridor->getNome(),
            ];
        }

        return new JsonResponse($descobridores);
    }

    /**
     * @Route("/associar-descobridor", name="associar_descobridor", methods="POST")
     */
    public function associarDescobridor(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();

        $input = [
            'especie_uuid' => empty($data['especie_uuid']) ? null : $data['especie_uuid'],
            'nome_descobridor_id' => empty($data['nome_descobridor_id']) ? null : $data['nome_descobridor_id'],
        ];

        $entityManager = $this->getDoctrine()->getManager();

        $especie = $entityManager->getRepository(Especie::class)->findOneBy(['uuid' => $input['especie_uuid']]);
        $descobridor = $entityManager->getRepository(Descobridor::class)->find($input['nome_descobridor_id']);

        if (!$especie || !$descobridor) {
            throw $this->createNotFoundException('The entity was not found');
        }

        $especie->addDescobridor($descobridor);

        $entityManager->persist($especie);
        $entityManager->flush();

        $qtdDescobridores = $entityManager->getRepository(Descobridor::class)->qtdDescobridoresAssocidos($input['especie_uuid']);

        return new JsonResponse([
            'qtd_descobridores' => $qtdDescobridores,
        ]);
    }

    /**
     * @Route("/listar-descobridor/{uuid}", name="listar_descobridor", methods="GET")
     */
   public function listarDescobridor($uuid): Response
   {
        $especie = $this->getDoctrine()->getRepository(Especie::class)->findOneBy(['uuid' => $uuid]);

        if (!$especie) {
            throw $this->createNotFoundException('The entity was not found');
        }

        $descobridor = $especie->getDescobridor();

        foreach ($descobridor as $descobridor) {
            $itens['itens'][] = [
                'id' => $descobridor->getId(),
                'title' => $descobridor->getNome()
            ];
        }

        return new JsonResponse($itens);
   }
}
