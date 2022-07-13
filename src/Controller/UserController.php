<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ValidatorManager;
use App\Entity\User;
use DateTime;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use PHPTokenGenerator\TokenGenerator;
use App\Entity\Token;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\SendEmail;
use function Symfony\Component\String\u;

/**
 * @Route("/api/especie/user", name="especie_", format="json")
 */
class UserController extends AbstractController
{
    private $validatorManager;
    private $requestStack;
    private $sendEmail;
    
    public function __construct(ValidatorManager $validatorManager, 
        RequestStack $requestStack, SendEmail $sendEmail)
    {
        $this->validatorManager = $validatorManager;
        $this->requestStack = $requestStack;
        $this->sendEmail = $sendEmail;
    }
    
    /**
     * @Route("/public/criar-user", name="criar_user", methods="POST")
     */
    public function criarUser(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = $request->request->all();
        $timeToken = new DateTime;
        $timeAccount = new DateTime;
        $hash = new TokenGenerator;

        $input = [
            'email' => empty($data['email']) ? '' : $data['email'],
            'password' => empty($data['password']) ? '' : $data['password'],
            'primeiro_nome' => empty($data['primeiro_nome']) ? '' : $data['primeiro_nome'],
            'sobrenome' => empty($data['sobrenome']) ? '' : $data['sobrenome'],
            'sexo' => empty($data['sexo']) ? '' : $data['sexo'],
            'data_nascimento' => empty($data['data_nascimento']) ? '' : DateTime::createFromFormat('d/m/Y', $data['data_nascimento']),
            'token' => $hash->generate(60),
            'time_token' => $timeToken->setTimestamp($timeToken->getTimestamp() + User::TEMPO_TOKEN),
            'time_account' => $timeAccount->setTimestamp($timeAccount->getTimestamp() + User::TEMPO_CONTA),
        ];

        $user = new User;

        $input['password'] = $passwordHasher->hashPassword(
            $user,
            $input['password']
        );
        
        $user->setEmail($input['email']);
        $user->setPassword($input['password']);
        $user->setPrimeiroNome($input['primeiro_nome']);
        $user->setSobrenome($input['sobrenome']);
        $user->setSexo($input['sexo']);
        $user->setStatus(User::PENDENTE);
        $user->setToken($input['token']);
        $user->setTimeToken($input['time_token']);
        $user->setTimeAccount($input['time_account']);
        $user->setDataNascimento($input['data_nascimento']);

        $errors = $this->validatorManager->validate($user);

        if ($errors->hasError()) {
            return $errors->response();
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($user);
        $entityManager->flush();

        // enviar email de ativação

        $search = ['{$input[\'token\']}', '{$user->getPrimeiroNome()}'];
        $replace = [$input['token'], $user->getPrimeiroNome()];

        $body = file_get_contents($this->getParameter('private_directory_emails') . '/' . 'ativar_conta.html'); 
        $body = str_replace($search, $replace, $body);

        $this->sendEmail->setAdrress($this->getParameter('email_suporte'), $input['email']);
        $this->sendEmail->setBody('Ativar conta', $body)->send();

        return new JsonResponse([
            'email' => $user->getEmail(),
        ]);
    }

    /**
     * @Route("/public/reenviar-token", name="reenviar_token", methods="POST")
     */
    public function reenviarTokenAtivacao(Request $request): Response 
    {
        $data = $request->request->all(); 
        $timeToken = new DateTime;
        $hash = new TokenGenerator;

        $input = [
            'email' => empty($data['email']) ? '' : u($data['email'])->trim()->lower(),
            'token' => $hash->generate(60),
        ];

        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $input['email']]);

        if (!$user) {
            throw $this->createNotFoundException(
                'The entity was not found.'
            );
        }

        if ($user->getStatus() == User::ATIVO || $user->getStatus() == User::INATIVO) {
            throw $this->createNotFoundException(
                'The entity was not found.'
            );
        }

        $user->setToken($input['token']);
        $user->setTimeToken($timeToken->setTimestamp($timeToken->getTimestamp() + User::TEMPO_TOKEN));

        $errors = $this->validatorManager->validate($user);

        if ($errors->hasError()) {
            return $errors->response();
        }

        $entityManager->persist($user);
        $entityManager->flush();

        // enviar email de ativação

        $search = ['{$input[\'token\']}', '{$user->getPrimeiroNome()}'];
        $replace = [$input['token'], $user->getPrimeiroNome()];

        $body = file_get_contents($this->getParameter('private_directory_emails') . '/' . 'ativar_conta.html'); 
        $body = str_replace($search, $replace, $body);

        $this->sendEmail->setAdrress($this->getParameter('email_suporte'), $input['email']);
        $this->sendEmail->setBody('Ativar conta', $body)->send();

        return new JsonResponse([
            'message' => 'The email was sent.'
        ]);
    }

    /**
     * @Route("/login", name="login", methods="POST")
     */
    public function login(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tokenGenerator = new TokenGenerator;

        $token = new Token;
        $tokenGenerator = $tokenGenerator->generate(60);

        $token->setUser($this->getUser());
        $token->setToken($tokenGenerator);

        $entityManager->persist($token);
        $entityManager->flush();
        
        return new JsonResponse([
            'token' => $tokenGenerator,
            'primeiro_nome' => $this->getUser()->getPrimeiroNome(), 
            'sobrenome' => $this->getUser()->getSobrenome(),
            'sexo' => $this->getUser()->getSexo(),
            'data_nascimento' => $this->getUser()->getDataNascimento()->format('d/m/Y'),
            'roles' => $this->getUser()->getRoles(),
        ]);
    }

    /**
     * @Route("/public/ativar-conta/{token}", name="ativar_conta", methods="GET")
     */
    public function ativarConta($token = null): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['token' => $token]);

        if (!$user) {
            throw $this->createNotFoundException(
                'The token was not found.'
            );
        }

        $timeToken = $user->getTimeToken();
        $timeAccount = $user->getTimeAccount();
        $currentTime = new DateTime;

        if ($currentTime->getTimestamp() > $timeAccount->getTimestamp()) {
            return new JsonResponse([
                'message' => 'There is not this account user.'
            ], 422);
        }

        if ($currentTime->getTimestamp() > $timeToken->getTimestamp()) {
            return new JsonResponse([
                'message' => 'This token is not valided.'
            ], 422);
        }

        $user->setStatus(User::ATIVO);
        $user->setToken(null);
        $user->setTimeToken(null);
        $user->setTimeAccount(null);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->flush();
        
        return new JsonResponse([
            'message' => 'You user account was actived.'
        ]);
    }

    /**
     * @Route("/ler-perfil", name="ler_perfil", methods="GET")
     */
    public function lerPerfil(): Response
    {
        $user = $this->getUser();

        return new JsonResponse([
            'primeiro_nome' => $user->getUser()->getPrimeiroNome(), 
            'sobrenome' => $user->getUser()->getSobrenome(),
            'sexo' => $user->getUser()->getSexo(),
            'data_nascimento' => $user->getUser()->getDataNascimento()->format('d/m/Y'),
            'roles' => $user->getUser()->getRoles(),
        ]);
    }

    /**
     * @Route("/editar-perfil", name="editar_perfil", methods="PUT")
     */
    public function editarPerfil(Request $request): Response
    {
        $data = $request->request->all();
        
        $input = [
            'primeiro_nome' => empty($data['primeiro_nome']) ? '' : $data['primeiro_nome'],
            'sobrenome' => empty($data['sobrenome']) ? '' : $data['sobrenome'],
            'sexo' => empty($data['sexo']) ? '' : $data['sexo'],
            'data_nascimento' => empty($data['data_nascimento']) ? '' : DateTime::createFromFormat('d/m/Y', $data['data_nascimento']),
        ];

        $entityManager = $this->getDoctrine()->getManager();
        
        $user = $this->getUser()->getUser();

        $user->setPrimeiroNome($input['primeiro_nome']);
        $user->setSobrenome($input['sobrenome']);
        $user->setSexo($input['sexo']);
        $user->setDataNascimento($input['data_nascimento']);
        

        $errors = $this->validatorManager->validate($user);

        if ($errors->hasError()) {
            return $errors->response();
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            'primeiro_nome' => $user->getPrimeiroNome(), 
            'sobrenome' => $user->getSobrenome(),
            'sexo' => $user->getSexo(),
            'roles' => $user->getRoles(),
        ]);
    }

    /**
     * @Route("/alterar-senha", name="alterar_senha", methods="PUT")
     */
    public function alterarSenha(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = $request->request->all();

        $input = [
            'senhaAnterior' => empty($data['senhaAnterior']) ? '' : $data['senhaAnterior'],
            'novaSenha' => empty($data['novaSenha']) ? '' : $data['novaSenha'],
        ];

        $entityManager = $this->getDoctrine()->getManager();
        
        $user = $this->getUser()->getUser();

        if ($passwordHasher->isPasswordValid($user, $input['senhaAnterior'])) {
            $novaSenha = $passwordHasher->hashPassword(
                $user,
                $input['novaSenha']
            );

            $user->setPassword($novaSenha);

            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse([
                'status' => 'The password was changed.'
            ]);
        } else {
            return new JsonResponse([
                'status' => 'The password was not changed.'
            ], 422);
        }
    }

    /**
     * @Route("/apagar-token/{token}", name="apagar_token", methods="DELETE")
     */
    public function apagarToken($token): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $token = $entityManager->getRepository(Token::class)->findOneBy(['token' => $token]);

        if (!$token) {
            throw $this->createNotFoundException(
                'The token was not found.'
            );
        }

        $entityManager->remove($token);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'You removed your token.'
        ]);
    }

    /**
     * @Route("/public/recuperar-conta", name="recuperar_conta", methods="POST")
     */
    public function recuperarConta(Request $request): Response
    {
        $data = $request->request->all(); 
        $timeToken = new DateTime;
        $hash = new TokenGenerator;

        $input = [
            'email' => empty($data['email']) ? '' : u($data['email'])->trim()->lower(),
            'token' => $hash->generate(120),
        ];

        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $input['email']]);

        if (!$user) {
            throw $this->createNotFoundException(
                'The entity was not found.'
            );
        }

        if ($user->getStatus() == User::PENDENTE) {
            throw $this->createNotFoundException(
                'The entity was not found.'
            );
        }

        $user->setChangePassword($input['token']);
        $user->setChangePasswordTime($timeToken->setTimestamp($timeToken->getTimestamp() + User::TEMPO_PASSWORD));

        $entityManager->persist($user);
        $entityManager->flush();

        // enviar email de ativação

        $search = ['{$input[\'token\']}', '{$user->getPrimeiroNome()}'];
        $replace = [$input['token'], $user->getPrimeiroNome()];

        $body = file_get_contents($this->getParameter('private_directory_emails') . '/' . 'password_conta.html'); 
        $body = str_replace($search, $replace, $body);

        $this->sendEmail->setAdrress($this->getParameter('email_suporte'), $input['email']);
        $this->sendEmail->setBody('Recuperar conta', $body)->send();

        return new JsonResponse([
            'message' => 'The email was sent.'
        ]);
    }

    /**
     * @Route("/public/recuperar-senha", name="recuperar_senha", methods="POST")
     */
    public function recuperarSenha(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = $request->request->all();

        $input = [
            'token' => empty($data['token']) ? '' : $data['token'],
            'password' => empty($data['password']) ? '' : $data['password'],
        ];  

        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository(User::class)->findOneBy(['change_password' => $input['token']]);

        if (!$user) {
            throw $this->createNotFoundException(
                'The entity was not found.'
            );
        }

        $timeToken = $user->getChangePasswordTime();
        $currentTime = new DateTime;

        if ($currentTime->getTimestamp() > $timeToken->getTimestamp()) {
            $user->setChangePassword(null);
            $user->setChangePasswordTime(null);

            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'The token is not valid.'
            ], 422);
        }

        $input['password'] = $passwordHasher->hashPassword(
            $user,
            $input['password']
        );

        $user->setPassword($input['password']);
        $user->setChangePassword(null);
        $user->setChangePasswordTime(null);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'The password was changed.'
        ]);
    }
}
