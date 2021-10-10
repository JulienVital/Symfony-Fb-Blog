<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AppCustomAuthenticator extends OAuth2Authenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;

    public function supports(Request $request): ?bool
    {
            
        return 'connect_facebook_check' === $request->attributes->get('_route') ;
    }

    public function __construct(private $fbPageId, private ClientRegistry $clientRegistry, private HttpClientInterface $httpClient, private EntityManagerInterface $entityManager )
    {

    }

    public function authenticate(Request $request): PassportInterface
    {
        $client = $this->clientRegistry->getClient('facebook');
        $accessToken = $this->fetchAccessToken($client);

        
        /** @var FacebookUser $facebookUser */
        $facebookUser = $client->fetchUserFromToken($accessToken);
        $userId = $facebookUser->getId();
        $uri = 'https://graph.facebook.com/me/accounts?access_token='.$accessToken;
		$response = $this->httpClient->request('GET',$uri)->toArray();

        $userAccessByPageID = FALSE;
		foreach($response['data'] as $page){
			if ($page['id']== $this->fbPageId){
				$userAccessByPageID = TRUE;
				$pageToken = $page['access_token'];

				break;
			}
		}
        
        if (!$userAccessByPageID){
            throw new CustomUserMessageAuthenticationException('Unauthorized');
        }


        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $facebookUser, $userId, $pageToken) {


                // 1) have they logged in with Facebook before? Easy!
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['fbId' => $userId]);
                if ($existingUser) {
                    return $existingUser;
                }

                $user = (new User())
                ->setFbId($facebookUser->getId())
                ->setUsername($facebookUser->getName())
                ->setPageToken($pageToken);
                $em = $this->entityManager;
                $em->persist($user);
                $em->flush($user);
                return $user;
            })
        );
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        //return new RedirectResponse($this->urlGenerator->generate('some_route'));
        throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        return new Response($message, Response::HTTP_FORBIDDEN);
    }
}
