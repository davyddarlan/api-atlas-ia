<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Especie;
use App\Entity\Marcador;
use App\Service\ValidatorManager;

/**
 * @Route("/api/especie/marcador", name="especie_", format="json")
 */
class MarcadorController extends AbstractController
{
    private $validatorManager;
    
    public function __construct(ValidatorManager $validatorManager) 
    {
        $this->validatorManager = $validatorManager;
    }
    
    /**
     * @Route("/criar-marcador", name="criar_marcador", methods="POST")
     */
    public function criarMarcador(Request $request): Response
    {  
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();

        $input = [
            'nome' => empty($data['nome']) ? '' : $data['nome'],
            'descricao' => empty($data['descricao']) ? null : $data['descricao'],
            'cor_marcador' => empty($data['cor_marcador']) ? null : $data['cor_marcador'],
        ];

        $marcador = new Marcador;

        $marcador->setNome($input['nome']);
        $marcador->setDescricao($input['descricao']);
        $marcador->setCorMarcador($input['cor_marcador']);

        $errors = $this->validatorManager->validate($marcador);

        if ($this->validatorManager->hasError($errors)) {
            return $this->validatorManager->response();
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($marcador);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $marcador->getId(),
            'nome' => $marcador->getNome(),
        ]);
    }

    /**
     * @Route("/buscar-marcador/{nome}", name="buscar_marcador", methods="GET")
     */
    public function buscarMarcador(Request $request, $nome = ''): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if (empty($nome)) {
            return new JsonResponse([]);
        }
        
        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->createQuery(
            'SELECT marcador 
            FROM App\Entity\Marcador marcador
            WHERE marcador.nome 
            LIKE :nome'
        )->setParameter('nome', $nome . '%');

        if (!count($query->getResult())) {
            return new JsonResponse([]);
        }

        foreach ($query->getResult() as $marcador) {
            $marcadores[] = [
                'id' => $marcador->getId(),
                'nome' => $marcador->getNome(),
            ];
        }

        return new JsonResponse($marcadores);
    }

    /**
     * @Route("/associar-marcador", name="associar_marcador", methods="POST")
     */
    public function associarMarcador(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();

        $input = [
            'especie_uuid' => empty($data['especie_uuid']) ? null : $data['especie_uuid'],
            'marcador_id' => empty($data['marcador_id']) ? null : $data['marcador_id'],
        ];

        $entityManager = $this->getDoctrine()->getManager();

        $especie = $entityManager->getRepository(Especie::class)->findOneBy(['uuid' => $input['especie_uuid']]);
        $marcador = $entityManager->getRepository(Marcador::class)->find($input['marcador_id']);

        if (!$especie || !$marcador) {
            throw $this->createNotFoundException('The entity was not found');
        }

        $especie->addMarcador($marcador);

        $entityManager->persist($especie);
        $entityManager->flush();

        $qtdMarcadores = $entityManager->getRepository(Marcador::class)->qtdMarcadoresAssocidos($input['especie_uuid']);

        return new JsonResponse([
            'qtd_marcadores' => $qtdMarcadores,
        ]);
    }

    /**
     * @Route("/listar-marcador/{uuid}", name="listar_marcador", methods="GET")
     */
   public function listarMarcador($uuid): Response
   {
        $especie = $this->getDoctrine()->getRepository(Especie::class)->findOneBy(['uuid' => $uuid]);

        if (!$especie) {
            throw $this->createNotFoundException('The entity was not found');
        }

        $marcador = $especie->getMarcador();

        foreach ($marcador as $marcador) {
            $itens['itens'][] = [
                'id' => $marcador->getId(),
                'title' => $marcador->getNome()
            ];
        }

        return new JsonResponse($itens);
   }
}
