<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Especie;
use App\Entity\Multimidia;
use \Exception;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\HeaderUtils;
use App\Service\ValidatorManager;
use Symfony\Component\Filesystem\Filesystem;
use App\Service\PersistMetaData;
use App\Entity\MetaDado;

/**
 * @Route("/api/especie/multimidia", name="especie_multimidia_", format="json")
 */
class MultimidiaController extends AbstractController
{
    private $validatorManager;
    private $filesystem;
    private $persistMetaData;
    
    public function __construct(
        ValidatorManager $validatorManager, 
        Filesystem $filesystem, 
        PersistMetaData $persistMetaData
        )
    {
        $this->validatorManager = $validatorManager;
        $this->filesystem = $filesystem;
        $this->persistMetaData = $persistMetaData;
    }
    
    /**
     * @Route("/adicionar-midia", name="adicionar_midia", methods="POST")
     */
    public function adicionarMidia(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        try {
            $data = $request->request->all();
            $arquivo = $request->files->get('arquivo');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->getConnection()->beginTransaction();

            $input = [
                'arquivo' => empty($arquivo) ? null : $arquivo,
                'especie_uuid' => empty($data['especie_uuid']) ? null : $data['especie_uuid'],
                'titulo' => empty($data['titulo']) ? '' : $data['titulo'],
                'descricao' => empty($data['descricao']) ? '' : $data['descricao'],
            ];

            $especie = $entityManager->getRepository(Especie::class)->findOneBy(['uuid' => $input['especie_uuid']]);

            if (!$especie || !$input['arquivo']) {
                throw $this->createNotFoundException('Something is wrong. Did you insert any file?');
            }
            
            $this->filesystem->mkdir($this->getParameter('private_directory_multimidia', 0777));

            $multimidia = new Multimidia;

            $multimidia->setEspecie($especie);
            $multimidia->setMultimidia($input['arquivo']);
            $multimidia->setTitulo($input['titulo']);
            $multimidia->setDescricao($input['descricao']);

            $metadados = [
                'real_extensao' => $input['arquivo']->getClientOriginalExtension(),
                'hash' => Uuid::v1(),
                'fake_extensao' => null,
            ];

            if ($metadados['real_extensao'] == 'png' || $metadados['real_extensao'] == 'jpg' || $metadados['real_extensao'] == 'jpeg') {
                $metadados['fake_extensao'] = 'jpeg';
                $locazacaoTemporaria = $input['arquivo']->getPath() . '/' . $input['arquivo']->getFilename();
            } else {
                $metadados['fake_extensao'] = $metadados['real_extensao'];
            }

            $metadados['nome'] = $metadados['hash'] . '.' . $metadados['fake_extensao'];

            $multimidia->setNome($metadados['nome']);

            $errors = $this->validatorManager->validate($multimidia);

            if ($this->validatorManager->hasError($errors)) {
                return $this->validatorManager->response();
            }

            if ($metadados['real_extensao'] == 'png') {
                $imagem = imagecreatefrompng($locazacaoTemporaria);

                imagejpeg($imagem, $this->getParameter('private_directory_multimidia') . '/' . $metadados['nome'], 75);
            } else if ($metadados['real_extensao'] == 'jpg' || $metadados['real_extensao'] == 'jpeg') {
                $imagem = imagecreatefromjpeg($locazacaoTemporaria);
                
                $exif = $this->persistMetaData->setExif($locazacaoTemporaria)->getExif();

                if ($exif) {
                    foreach ($exif as $data) {
                        $metaDado = new MetaDado;

                        $metaDado->setNome($data['nome']);
                        $metaDado->setValor($data['valor']);
                        $multimidia->addMetadado($metaDado);

                        $entityManager->persist($metaDado);
                    }   
                }

                imagejpeg($imagem, $this->getParameter('private_directory_multimidia') . '/' . $metadados['nome'], 75);
            } else {
                $input['arquivo']->move($this->getParameter('private_directory_multimidia'), $metadados['nome']);
            }

            $entityManager->persist($multimidia);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
        } catch (Exception $e) {
            $entityManager->getConnection()->rollBack();
            
            return new JsonResponse($e->getMessage(), 500);
        }

        return new JsonResponse([
            'file' => $metadados['nome'],
        ]);
    }

    /**
     * @Route("/listar-midias/{uuid}", name="listar_midias", methods="GET")
     */
    public function listarMidias($uuid): Response
    {
        $especie = $this->getDoctrine()->getRepository(Especie::class)->findOneBy(['uuid' => $uuid]);

        if (!$especie) {
            throw $this->createNotFoundException('The entity was not found.');
        }

        $midias = [];

        foreach ($especie->getMultimidia() as $midia) {
            $midias[] = [
                'nome' => $midia->getNome(),
                'titulo' => $midia->getTitulo(),
                'descricao' => $midia->getDescricao(),
            ];
        }

        return new JsonResponse($midias);
    }

    /**
     * @Route("/abrir-midia/{nome}", name="abrir_midia", methods="GET")
     */
    public function abrirMidia($nome): Response
    {
        $midia = $this->getDoctrine()->getRepository(Multimidia::class)->findOneBy(['nome' => $nome]);

        if (!$midia) {
            throw $this->createNotFoundException('The entity was not found.');
        }

        $caminho = $this->getParameter('private_directory_multimidia') . '/' . $midia->getNome();
        
        $recurso = new File($caminho);
        $response = new Response($recurso->getContent());

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_INLINE,
            $midia->getNome(),
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', $recurso->getMimeType());

        return $response;
    }

    /**
     * @Route("/apagar-midia/{nome}", name="apagar_midia", methods="DELETE")
     */
    public function apagarMidia($nome): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $midia = $this->getDoctrine()->getRepository(Multimidia::class)->findOneBy(['nome' => $nome]);

        if (!$midia) {
            throw $this->createNotFoundException('The entity was not found.');
        }
        
        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->getConnection()->beginTransaction();

            $this->filesystem->remove($this->getParameter('private_directory_multimidia') . '/' . $midia->getNome());

            $entityManager->remove($midia);
            $entityManager->flush();

            $entityManager->getConnection()->commit();
        } catch (Exception $e) {
            $entityManager->getConnection()->rollback();

            return new JsonResponse($e->getMessage(), 500);
        }

        return new JsonResponse([
            'nome' => $midia->getNome(),
        ]);
    }

    /**
     * @Route("/editar-dados-principais", name="editar_dados_principais", methods="PUT")
     */
    public function editarDadosPrincipais(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = $request->request->all();

        $input = [
            'nome' => empty($data['nome']) ? null : $data['nome'],
            'titulo' => empty($data['titulo']) ? '' : $data['titulo'],
            'descricao' => empty($data['descricao']) ? '' : $data['descricao'],
        ];

        $entityManager = $this->getDoctrine()->getManager();
        
        $midia = $entityManager->getRepository(Multimidia::class)->findOneBy(['nome' => $input['nome']]);

        if (!$midia) {
            throw $this->createNotFoundException('The entity was not found.');
        }

        $midia->setTitulo($input['titulo']);
        $midia->setDescricao($input['descricao']);

        $entityManager->flush();

        return new JsonResponse([
            'titulo' => $midia->getTitulo(),
            'descricao' => $midia->getDescricao(),
        ]);
    }
}
