<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Especie;
use App\Entity\NomePopular;
use App\Entity\Descobridor;
use App\Entity\Marcador;
use App\Entity\Cladograma;
use Symfony\Component\Uid\Uuid;
use \Exception;
use App\Service\ValidatorManager;

use App\Entity\Ordem;
use App\Entity\Familia;
use App\Entity\SubFamilia;
use App\Entity\EstadoConservacao;
use function Symfony\Component\String\u;

/**
 * @Route("/api/especie", name="especie_", format="json")
 */
class EspecieController extends AbstractController
{        
    private $validatorManager;

    public function __construct(ValidatorManager $validatorManager) 
    {
        $this->validatorManager = $validatorManager;
    }

    /**
     * @Route("/public/verificar-especie/{uuid}", name="verificar_especie", methods="GET")
     */
    public function verificarEspecie($uuid): Response
    {
        $especie = $this->getDoctrine()->getRepository(Especie::class)->findOneBy(['uuid' => $uuid]);

        if (!$especie) {
            throw $this->createNotFoundException(
                'The entity was not found.'
            );
        }
        
        return new JsonResponse([
            'nome' => $especie->getNomeCientifico(),
        ]);
    }
    
    /**
     * @Route("/public/buscar-especie", name="buscar_especie", methods="GET")
     */
    public function buscarEspecie(Request $request): Response
    {
        $data = $request->query->all();

        $input = [
            'nome_especie' => empty($data['nome_especie']) ? null : $data['nome_especie'],
        ];

        if (empty($input['nome_especie'])) {
            return new JsonResponse([]);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->createQuery(
            'SELECT especie 
            FROM App\Entity\Especie especie
            WHERE especie.nome_cientifico 
            LIKE :nome_cientifico'
        )->setParameter('nome_cientifico', $input['nome_especie'] . '%');

        if (!count($query->getResult())) {
            return new JsonResponse([]);
        }

        foreach ($query->getResult() as $especie) {
            $especies[] = [
                'uuid' => $especie->getUuid(),
                'nome_cientifico' => $especie->getNomeCientifico(),
                'descricao' => empty($especie->getDescricao()) ? '' : $especie->getDescricao(),
            ];
        }

        return new JsonResponse($especies);
    }
    
    /**
     * @Route("/criar-especie", name="criar_especie", methods="POST")
     */
    public function criarEspecie(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();
        $entityManager = $this->getDoctrine()->getManager();
        $entityListValidator = [];

        $input = [
            'nome_cientifico' => empty($data['nome_cientifico']) ? '' : $data['nome_cientifico'],
            'nome_popular' => empty($data['nome_popular']) ? null : $data['nome_popular'],
            'nome_ingles' => empty($data['nome_ingles']) ? null : $data['nome_ingles'],
            'nome_descobridor' => empty($data['nome_descobridor']) ? null : $data['nome_descobridor'],
            'ano_descoberta' => empty($data['ano_descoberta']) ? null : $data['ano_descoberta'],
            'descricao' => empty($data['descricao']) ? null : $data['descricao'], 
        ];
        
        $especie = new Especie;

        if (!empty($input['nome_popular'])) {
            $nomePopular = new NomePopular;
            $nomePopular->setNome($input['nome_popular']);

            $especie->addNomePopular($nomePopular);

            $entityListValidator[] = $nomePopular;

            $entityManager->persist($nomePopular);
        }

        if (!empty($input['nome_descobridor'])) {
            $descobridor = new Descobridor;
            $descobridor->setNome($input['nome_descobridor']);
            
            $especie->addDescobridor($descobridor);

            $entityListValidator[] = $descobridor;

            $entityManager->persist($descobridor);
        }

        $especie->setUuid(Uuid::v4());
        $especie->setNomeCientifico($input['nome_cientifico']);
        $especie->setPrincipalNomePopular($input['nome_popular']);
        $especie->setNomeIngles($input['nome_ingles']);
        $especie->setAnoDescoberta($input['ano_descoberta']);
        $especie->setDescricao($input['descricao']);
        $especie->setCladograma(new Cladograma);

        $entityListValidator[] = $especie;

        $errors = $this->validatorManager->validate($entityListValidator);

        if ($this->validatorManager->hasError($errors)) {
            return $this->validatorManager->response();
        }

        $entityManager->persist($especie);
        $entityManager->flush();
        
        return new JsonResponse([
            'uuid' => $especie->getUuid(),
            'nome_cientifico' => $especie->getNomeCientifico(),
        ]);
    }

    /**
     * @Route("/public/exibir-principais-dados/{uuid}", name="exibir_principais_dados", methods="GET")
     */
    public function exibirPrincipaisDados($uuid): Response
    {
        $especie = $this->getDoctrine()->getRepository(Especie::class)->findOneBy(['uuid' => $uuid]);

        $output = [
            'nome_popular' => empty($especie->getPrincipalNomePopular()) ? '' : $especie->getPrincipalNomePopular(),
            'nome_cientifico' => empty($especie->getNomeCientifico()) ? '' : $especie->getNomeCientifico(),
            'descricao' => empty($especie->getDescricao()) ? '' : $especie->getDescricao(),
            'capa' => empty($especie->getCapa()) ? '' : $especie->getCapa(),
        ];

        return new JsonResponse($output);
    }

    /**
     * @Route("/adicionar-capa", name="adicionar_capa", methods="POST")
     */
    public function adicionarCapa(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();
        $file = $request->files->get('capa');

        $input = [
            'especie_uuid' => empty($data['especie_uuid']) ? null : $data['especie_uuid'],
            'capa' => empty($file) ? null : $file,
        ];

        if (empty($input['capa'])) {
            return new JsonResponse(['sem dados']);
        }

        $metadados = [
            'extensao' => empty($input['capa']->getClientOriginalExtension()) ? 'jpeg' : $input['capa']->getClientOriginalExtension(),
            'hash' => Uuid::v1(),
        ];

        $metadados['nome'] = $metadados['hash'] . '.' . $metadados['extensao'];

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->getConnection()->beginTransaction();
            
            $especie = $entityManager->getRepository(Especie::class)->findOneBy(['uuid' => $input['especie_uuid']]);

            $especie->setCapa($metadados['nome']);
            $especie->setMultimidiaCapa($input['capa']);

            $errors = $this->validatorManager->validate($especie);

            if ($this->validatorManager->hasError($errors)) {
                return $this->validatorManager->response();
            }    

            $entityManager->persist($especie);
            $entityManager->flush();

            $input['capa']->move($this->getParameter('public_directory_capas'), $metadados['nome']);

            $entityManager->getConnection()->commit();
        } catch (Exception $e) {
            $entityManager->getConnection()->rollBack();
        }

        return new JsonResponse($metadados);
    }

    /**
     * @Route("/alterar-dados-especie", name="alterar_dados_capa", methods="PUT")
     */
    public function alterarDadosCapa(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();

        $input = [
            'especie_uuid' => empty($data['especie_uuid']) ? null : $data['especie_uuid'],
            'nome_cientifico' => empty($data['nome_cientifico']) ? null : $data['nome_cientifico'],
            'nome_popular' => empty($data['nome_popular']) ? null : $data['nome_popular'],
            'descricao' => empty($data['descricao']) ? null : $data['descricao'], 
        ];

        $entityManager = $this->getDoctrine()->getManager();

        $especie = $entityManager->getRepository(Especie::class)->findOneBy(['uuid' => $input['especie_uuid']]);

        $especie->setNomeCientifico($input['nome_cientifico']);
        $especie->setPrincipalNomePopular($input['nome_popular']);
        $especie->setDescricao($input['descricao']);

        $entityManager->persist($especie);
        $entityManager->flush();
        
        return new JsonResponse([
            'uuid' => $especie->getUuid(),
            'nome_cientifico' => $especie->getNomeCientifico(),
            'nome_popular' => $especie->getPrincipalNomePopular(),
            'descricao' => $especie->getDescricao(),
        ]);
    }

    /**
     * @Route("/alterar-dados-gerais", name="alterar_dados_gerais", methods="PUT")
     */
    public function AlterarDadosGerais(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();

        $input = [
            'especie_uuid' => empty($data['especie_uuid']) ? null : $data['especie_uuid'],
            'nome_cientifico' => empty($data['nome_cientifico']) ? null : $data['nome_cientifico'],
            'nome_ingles' => empty($data['nome_ingles']) ? null : $data['nome_ingles'],
            'ano_descoberta' => empty($data['ano_descoberta']) ? null : $data['ano_descoberta'],
        ];

        $entityManager = $this->getDoctrine()->getManager();

        $especie = $entityManager->getRepository(Especie::class)->findOneBy(['uuid' => $input['especie_uuid']]);

        $especie->setNomeCientifico($input['nome_cientifico']);
        $especie->setNomeIngles($input['nome_ingles']);
        $especie->setAnoDescoberta($input['ano_descoberta']);

        $entityManager->persist($especie);
        $entityManager->flush();

        return new JsonResponse([
            'uuid' => $especie->getUuid(),
            'nome_cientifico' => $especie->getNomeCientifico(),
            'nome_ingles' => $especie->getNomeIngles(),
            'ano_descoberta' => $especie->getAnoDescoberta(),
        ]);
    }

    /**
     * @Route("/exibir-dados-gerais/{uuid}", name="exibir_dados_gerais", methods="GET")
     */
    public function exibirDadosGerais($uuid): Response
    {
        $especie = $this->getDoctrine()->getRepository(Especie::class)->findOneBy(['uuid' => $uuid]);
        $nomePopular = $this->getDoctrine()->getRepository(NomePopular::class);
        $descobridor = $this->getDoctrine()->getRepository(Descobridor::class);
        $marcador = $this->getDoctrine()->getRepository(Marcador::class);

        $output = [
            'nome_cientifico' => empty($especie->getNomeCientifico()) ? null : $especie->getNomeCientifico(),
            'nome_ingles' => empty($especie->getNomeIngles()) ? null : $especie->getNomeIngles(),
            'ano_descoberta' => empty($especie->getAnoDescoberta()) ? null : $especie->getAnoDescoberta(),
            'estado_conservacao' => empty($especie->getEstadoConservacao()) ? null : $especie->getEstadoConservacao()->getNome(),
            'qtd_nomes_populares' => $nomePopular->qtdNomesPopularesAssocidos($uuid),
            'qtd_descobridores' => $descobridor->qtdDescobridoresAssocidos($uuid),
            'qtd_marcadores' => $marcador->qtdMarcadoresAssocidos($uuid),
        ];
        
        return new JsonResponse([
            'nome_cientifico' => $output['nome_cientifico'],
            'nome_ingles' => $output['nome_ingles'],
            'ano_descoberta' => $output['ano_descoberta'],
            'estado_conservacao' => $output['estado_conservacao'],
            'qtd_nomes_populares' => $output['qtd_nomes_populares'],
            'qtd_descobridores' => $output['qtd_descobridores'],
            'qtd_marcadores' => $output['qtd_marcadores'],
        ]);
    }

    /**
     * @Route("/public/teste", name="teste", methods="GET")
     */
    public function teste(): Response 
    {
        $entityManager = $this->getDoctrine()->getManager();

        /*$estados = [
            'quase ameaçada', 'vulnerável', 'em perigo', 
            'em perigo crítico', 'possivelmente extinta na natureza',
            'possivelmente extinta', 'extinta na natureza',
            'extinta',
        ];*/
        
        if (($handle = fopen($this->getParameter('private_directory_especies') . 'teste.csv', 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
                $entityManager = $this->getDoctrine()->getManager();

                $nome = (string) u($data[0])->trim()->lower()->title(true);
                $especie = $entityManager->getRepository(Especie::class)->findOneBy(['nome_cientifico' => $nome]);

                // ordem
                if ($data[1]) {
                    $nome = (string) u($data[1])->trim()->lower()->title(true);

                    $ordem = $entityManager->getRepository(Ordem::class)->findOneBy(['nome' => $nome]);

                    $especie->getCladograma()->setOrdem($ordem);
                }

                // subfamilia
                if ($data[2]) {
                    $nome = (string) u($data[2])->trim()->lower()->title(true);

                    $subFamilia = $entityManager->getRepository(SubFamilia::class)->findOneBy(['nome' => $nome]);

                    $especie->getCladograma()->setSubfamilia($subFamilia);
                }

                // familia
                if ($data[3]) {
                    $nome = (string) u($data[3])->trim()->lower()->title(true);

                    $familia = $entityManager->getRepository(Familia::class)->findOneBy(['nome' => $nome]);

                    $especie->getCladograma()->setFamilia($familia);
                }
                

                // nome popular
                if ($data[4]) {
                    $nome = (string) u($data[4])->trim()->lower()->title(true);

                    $nomePopular = $entityManager->getRepository(NomePopular::class)->findOneBy(['nome' => $nome]);

                    $especie->addNomePopular($nomePopular);
                }

                // nome popular
                if ($data[6]) {
                    switch ($data[6]) {
                        case 'NT':
                            $nome = 'Quase Ameaçada';
                        break;
                        case 'VU':
                            $nome = 'Vulnerável';
                        break;
                        case 'EN':
                            $nome = 'Em Perigo';
                        break;
                        case 'CR':
                            $nome = 'Em Perigo Crítico';
                        break;
                        case 'CR(PEW)':
                            $nome = 'Possivelmente Extinta Na Natureza';
                        break;
                        case 'CR(PE)':
                            $nome = 'Possivelmente Extinta';
                        break;
                        case 'EW':
                            $nome = 'Extinta Na Natureza';
                        break;
                        case 'EX':
                            $nome = 'Extinta';
                        break;
                    }

                    $estado_conservacao = $entityManager->getRepository(EstadoConservacao::class)->findOneBy(['nome' => $nome]);

                    $especie->setEstadoConservacao($estado_conservacao);
                }
                
                $entityManager->persist($especie);
                $entityManager->flush();
            }

            fclose($handle);
        }  
        
        return new JsonResponse([
            'message' => 'ok',
        ]);
    }
}
