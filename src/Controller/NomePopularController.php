<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Especie;
use App\Entity\NomePopular;
use App\Service\ValidatorManager;

/**
 * @Route("/api/especie/nome-popular", name="especie_", format="json")
 */
class NomePopularController extends AbstractController
{
    private $validatorManager;

    public function __construct(ValidatorManager $validatorManager)
    {
        $this->validatorManager = $validatorManager;
    }
    
    /**
     * @Route("/criar-nome-popular", name="criar_nome-popular", methods="POST")
     */
    public function criarNomePopular(Request $request): Response
    {  
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();

        $input = [
            'nome' => empty($data['nome']) ? '' : $data['nome'],
        ];

        $nomePopular = new NomePopular;

        $nomePopular->setNome($input['nome']);

        $errors = $this->validatorManager->validate($nomePopular);

        if ($this->validatorManager->hasError($errors)) {
            return $this->validatorManager->response();
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($nomePopular);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $nomePopular->getId(),
            'nome' => $nomePopular->getNome(),
        ]);
    }

    /**
     * @Route("/buscar-nome-popular/{nome}", name="buscar_nome-popular", methods="GET")
     */
    public function buscarNomePopular(Request $request, $nome = ''): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if (empty($nome)) {
            return new JsonResponse([]);
        }
        
        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->createQuery(
            'SELECT nomePopular 
            FROM App\Entity\NomePopular nomePopular
            WHERE nomePopular.nome 
            LIKE :nome'
        )->setParameter('nome', $nome . '%');

        if (!count($query->getResult())) {
            return new JsonResponse([]);
        }

        foreach ($query->getResult() as $nomePopular) {
            $nomesPopulares[] = [
                'id' => $nomePopular->getId(),
                'nome' => $nomePopular->getNome(),
            ];
        }

        return new JsonResponse($nomesPopulares);
    }

    /**
     * @Route("/associar-nome-popular", name="associar_nome_popular", methods="POST")
     */
    public function associarNomePopular(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();

        $input = [
            'especie_uuid' => empty($data['especie_uuid']) ? null : $data['especie_uuid'],
            'nome_popular_id' => empty($data['nome_popular_id']) ? null : $data['nome_popular_id'],
        ];

        $entityManager = $this->getDoctrine()->getManager();

        $especie = $entityManager->getRepository(Especie::class)->findOneBy(['uuid' => $input['especie_uuid']]);
        $nomePopular = $entityManager->getRepository(NomePopular::class)->find($input['nome_popular_id']);

        if (!$especie || !$nomePopular) {
            throw $this->createNotFoundException('The entity was not found.');
        }

        $especie->addNomePopular($nomePopular);

        $entityManager->persist($especie);
        $entityManager->flush();

        $qtdNomesPopulares = $entityManager->getRepository(NomePopular::class)->qtdNomesPopularesAssocidos($input['especie_uuid']);

        return new JsonResponse([
            'qtd_nomes_populares' => $qtdNomesPopulares,
        ]);
    }

    /**
     * @Route("/listar-nome-popular/{uuid}", name="listar_nome_popular", methods="GET")
     */
    public function listarNomePopular($uuid): Response
    {
        $especie = $this->getDoctrine()->getRepository(Especie::class)->findOneBy(['uuid' => $uuid]);

        if (!$especie) {
            throw $this->createNotFoundException('The entity was not found');
        }

        $nomePopular = $especie->getNomePopular();

        foreach ($nomePopular as $nomePopular) {
            $itens['itens'][] = [
                'id' => $nomePopular->getId(),
                'title' => $nomePopular->getNome()
            ];
        }

        return new JsonResponse($itens);
    }
}
