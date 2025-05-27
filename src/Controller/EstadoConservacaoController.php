<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\EstadoConservacao;
use App\Entity\Especie;
use \DateTime;
use App\Service\ValidatorManager;

/**
 * @Route("/api/especie/estado-conservacao", name="especie_", format="json")
 */
class EstadoConservacaoController extends AbstractController
{
    private $validatorManager;
    
    public function __construct(ValidatorManager $validatorManager)  
    {
        $this->validatorManager = $validatorManager;
    }
    
    /**
     * @Route("/criar-estado-conservacao", name="criar_estado_conservacao", methods="POST")
     */
    public function criarEstadoConservacao(Request $request) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();

        $input = [
            'nome' => empty($data['nome']) ? '' : $data['nome'],
            'descricao' => empty($data['descricao']) ? null : $data['descricao'],
        ];

        $estadoConservacao = new EstadoConservacao;

        $estadoConservacao->setNome($input['nome']);
        $estadoConservacao->setDescricao($input['descricao']);

        $errors = $this->validatorManager->validate($estadoConservacao);

        if ($this->validatorManager->hasError()) {
            return $this->validatorManager->response();
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($estadoConservacao);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $estadoConservacao->getId(),
            'nome' => $estadoConservacao->getNome(),
        ]);
    }

    /**
     * @Route("/buscar-estado-conservacao/{nome}", name="buscar_estado_conservacao", methods="GET")
     */
    public function buscarEstadoConservacao($nome = null)
    {        
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if (empty($nome)) {
            return new JsonResponse([]);
        }
        
        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->createQuery(
            'SELECT estadoConservacao 
            FROM App\Entity\EstadoConservacao estadoConservacao
            WHERE estadoConservacao.nome 
            LIKE :nome'
        )->setParameter('nome', $nome . '%');

        if (!count($query->getResult())) {
            return new JsonResponse([]);
        }

        foreach ($query->getResult() as $estadoConservacao) {
            $estadosConservacao[] = [
                'id' => $estadoConservacao->getId(),
                'nome' => $estadoConservacao->getNome(),
            ];
        }

        return new JsonResponse($estadosConservacao);
    }

    /**
     * @Route("/associar-estado-conservacao", name="associar_estado_conservacao", methods="POST")
     */
    public function associarEstadoConservacao(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = $request->request->all();

        $input = [
            'especie_uuid' => empty($data['especie_uuid']) ? null : $data['especie_uuid'],
            'estado_id' => empty($data['estado_id']) ? null : $data['estado_id'],
        ];

        $entityManager = $this->getDoctrine()->getManager();

        $especie = $entityManager->getRepository(Especie::class)->findOneBy(['uuid' => $input['especie_uuid']]);
        $estadoConservacao = $entityManager->getRepository(EstadoConservacao::class)->find($input['estado_id']);

        if (!$especie || !$estadoConservacao) {
            throw $this->createNotFoundException('The entity was not found');
        }

        $especie->setEstadoConservacao($estadoConservacao);
        
        $entityManager->persist($especie);
        $entityManager->flush();

        return new JsonResponse($input);
    }
}
