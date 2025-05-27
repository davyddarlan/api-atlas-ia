<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Especie;

/**
 * @Route("/api/especie/cladograma", name="especie_", format="json")
 */
class CladogramaController extends AbstractController
{
    /**
     * @Route("/obter-cladograma/{uuid}", name="obter_cladograma", methods="GET")
     */
    public function obterCladograma($uuid): Response
    {
        $especie = $this->getDoctrine()->getRepository(Especie::class)->findOneBy(['uuid' => $uuid]);

        if (!$especie) {
            throw $this->createNotFoundException('The entity was not found.');
        }

        $cladograma = $especie->getCladograma();
        
        $output = [
            'reino' => empty($cladograma->getReino()) ? '' : $cladograma->getReino()->getNome(),
            'filo' => empty($cladograma->getFilo()) ? '' : $cladograma->getFilo()->getNome(),
            'divisao' => empty($cladograma->getDivisao()) ? '' : $cladograma->getDivisao()->getNome(),
            'classe' => empty($cladograma->getClasse()) ? '' : $cladograma->getClasse()->getNome(),
            'subclasse' => empty($cladograma->getSubClasse()) ? '' : $cladograma->getSubClasse()->getNome(),
            'ordem' => empty($cladograma->getOrdem()) ? '' : $cladograma->getOrdem()->getNome(),
            'familia' => empty($cladograma->getFamilia()) ? '' : $cladograma->getFamilia()->getNome(),
            'subfamilia' => empty($cladograma->getSubFamilia()) ? '' : $cladograma->getSubFamilia()->getNome(),
            'genero' => empty($cladograma->getGenero()) ? '' : $cladograma->getGenero()->getNome(),
        ];

        return new JsonResponse($output);
    }

    /**
     * @Route("/associar-clado/{clado}", name="associar_clado", methods="POST")
     */
    public function associarClado(Request $request, $clado = null): Response
    {
        $data = $request->request->all();
        
        $input = [
            'uuid_especie' => empty($data['uuid_especie']) ? '' : $data['uuid_especie'],
            'id_clado' => empty($data['id_clado']) ? '' : $data['id_clado'],
        ];

        $entityManager = $this->getDoctrine()->getManager();
        $especie = $entityManager->getRepository(Especie::class)->findOneBy(['uuid' => $input['uuid_especie']]);

        if (empty($especie)) {
            throw $this->createNotFoundException('The entity was not found.');
        }
        
        $cladoInArray = in_array($clado, ['reino', 'filo', 'divisao', 'classe', 'subclasse', 
            'ordem', 'familia', 'subfamilia', 'genero']);
    
        if (empty($cladoInArray)) {
            throw $this->createNotFoundException('The entity was not found.');
        }

        $clado = ucwords($clado);

        if ($clado == 'Subclasse') {
            $clado = 'SubClasse';
        } else if ($clado == 'Subfamilia') {
            $clado = 'SubFamilia';
        }

        $entity = $entityManager->getRepository("\\App\\Entity\\" . $clado)->find($input['id_clado']);

        if (empty($entity)) {
            throw $this->createNotFoundException('The entity was not found.');
        }

        if ($clado == 'SubClasse') {
            $clado = 'Subclasse';
        } else if ($clado == 'SubFamilia') {
            $clado = 'Subfamilia';
        }

        $setData = 'set' . $clado;
        $getData = 'get' . $clado;

        $especie->getCladograma()->$setData($entity);

        $entityManager->flush();

        return new JsonResponse([
            'nome' => $especie->getCladograma()->$getData()->getNome(),
        ]);
    } 
}
