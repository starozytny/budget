<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Entity\User;
use App\Service\CalendarService;
use App\Service\CheckTime;
use App\Service\Mailer;
use App\Service\SettingsService;
use App\Service\Validation;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/connexion", options={"expose"=true}, name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            if ($this->isGranted('ROLE_SUPER_ADMIN')){
                return $this->redirectToRoute('user_dashboard');
            }else if ($this->isGranted('ROLE_ADMIN')){
                return $this->redirectToRoute('admin_dashboard');
            }else if ($this->isGranted('ROLE_USER')){
                return $this->redirectToRoute('user_dashboard');
            }
            return $this->redirectToRoute('app_homepage');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('root/app/pages/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/creation-de-compte", options={"expose"=true}, name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, CalendarService $calendarService)
    {
        if($request->isMethod("POST")){
            $em = $this->getDoctrine()->getManager();
            $data = json_decode($request->getContent());
            $username = $data->username->value;
            $email = $data->email->value;
            $password = $data->password->value;
            $passwordConfirme = $data->passwordConfirme->value;
            $solde = $data->solde->value;

            if($password != $passwordConfirme){
                return new JsonResponse(['code' => 0, 'message' => 'Les mots de passe ne sont pas identiques.']);
            }

            $existe = $em->getRepository(User::class)->findOneBy(['username' => $username]);
            if($existe){
                return new JsonResponse(['code' => 0, 'message' => 'Ce nom d\'utilisateur existe d??j??.']);
            }

            $existe = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if($existe){
                return new JsonResponse(['code' => 0, 'message' => 'Cet adresse e-mail existe d??j??.']);
            }

            $user = (new User())
                ->setUsername($username)
                ->setEmail($email)
                ->setIsNew(false)
            ;

            $user->setPassword($passwordEncoder->encodePassword($user, $password));

            $em->persist($user);

            $today = $calendarService->getToday();
            for($m=1 ; $m<=12 ; $m++){
                $init = ($m < $today['mon']) ? 0 : $solde;
                
                $budget = (new Budget())
                    ->setYear($today['year'])
                    ->setMonth($m)
                    ->setToSpend($init)
                    ->setInitMonth($init)
                    ->setUser($user)
                ;
                
                $em->persist($budget);
            }

            $em->flush();
            $url = $this->generateUrl('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
            return new JsonResponse(['code' => 1, 'message' => 'Cr??ation r??ussie. La page va se rafraichir automatiquement.', 'url' => $url]);
        }

        return $this->render('root/app/pages/security/register.html.twig');
    }

    /**
     * @Route("/deconnexion", options={"expose"=true}, name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/mot-de-passe-oublie/{user}", options={"expose"=true}, name="app_password_lost")
     */
    public function lost(Request $request, Mailer $mailer, SettingsService $settingsService, CheckTime $checkTime, User $user = null)
    {
        $em = $this->getDoctrine()->getManager();

        // if come from form
        if($user == null){
            // Check if User existe 
            $data = json_decode($request->getContent());
            $email = $data->email->value;
            $user = $em->getRepository(User::class)->findOneBy(array('email' => $email));
            if(!$user){
                return new JsonResponse([
                    'code' => 0,
                    'errors' => [
                        'success' => '', 
                        'error' => '', 
                        'email' => ['value' => $data->email->value, 'error' => 'Cette adresse e-mail est invalide.']
                    ]
                ]);
            }
            if($user->getPasswordTime()){
                if(!$checkTime->moreThirtyMinutes($user->getPasswordTime())){
                    return new JsonResponse(['code' => 1, 'message' => 'Un lien de r??initialisation a d??j?? ??t?? envoy??.']);
                }
            }
        }

        // Prepare values password code
        $code = uniqid();
        $user->setPasswordCode($code);
        $user->setPasswordTime(new DateTime());

        // Send mail
        $url = $this->generateUrl('app_password_reinit', ['token' => $user->getToken(), 'code' => $code], UrlGeneratorInterface::ABSOLUTE_URL);        
        if($mailer->sendMail(
            'Mot de passe oubli?? pour le site ' . $settingsService->getWebsiteName(),
            'Lien de r??initialisation de mot de passe',
            'root/app/email/security/lost.html.twig',
            ['url' => $url, 'user' => $user, 'settings' => $settingsService->getSettings()],
            $user->getEmail()
        ) != true){
            return new JsonResponse([
                'code' => 2,
                'errors' => [ 'error' => 'Le service est indisponible', 'success' => '' ]
            ]);
        }

        // Update User with code password and time
        $em->persist($user); $em->flush();
        
        $url = $this->generateUrl('app_login', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse(['code' => 1, 'message' => 'Un lien de r??initialisation a ??t?? envoy??. La page va se rafraichir automatiquement.', 'url' => $url]);
    }
    
    /**
     * @Route("/reinitialisation-mot-de-passe/{token}-{code}", name="app_password_reinit")
     */
    public function reinit(Request $request, $token, $code, CheckTime $checkTime, UserPasswordEncoderInterface $passwordEncoder, Validation $validation)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(array('token' => $token));
        if(!$user){ return $this->redirectToRoute('app_login'); }

        // If lien n'est plus valide a cause du temps expir??
        if($checkTime->moreThirtyMinutes($user->getPasswordTime())){
            $user->setPasswordCode(null);
            $user->setPasswordTime(null);

            $em->persist($user);$em->flush();
            return $this->render('root/app/pages/security/reinit_expired.html.twig', ['message' => 'Le lien a expir??. Veuillez recommencer la proc??dure.']);
        }
        // If code invalide
        if($user->getPasswordCode() != $code){
            return $this->render('root/app/pages/security/reinit_expired.html.twig', ['message' => 'Le lien n\'est pas valide ou a expir??.']);
        }

        // Form submitted
        if($request->isMethod('POST')){
            $data = json_decode($request->getContent());
            $password = $data->password->value;

            // validate password not empty and equal
            $resultat = $validation->validatePassword($password, $data->password2->value);         
            if($resultat != 1){
                return new JsonResponse(['code' => 2, 'errors' => [ 'error' => $resultat, 'success' => '' ]]);
            }

            $user = $this->setUserData($user, $password, $passwordEncoder);

            $url = $this->generateUrl('app_login', array(), UrlGeneratorInterface::ABSOLUTE_URL);
            return new JsonResponse(['code' => 1, 'message' => 'Le mot de passe a ??t?? r??initialis??. La page va se rafraichir automatiquement.', 'url' => $url]);
        }
        return $this->render('root/app/pages/security/reinit.html.twig', ['token' => $token, 'code' => $code, 'type' => 'reinit']);
    }

    
    /**
     * @Route("/renouvellement-mot-de-passe/{token}-{code}", name="app_password_renouv")
     */
    public function renouv(Request $request, $token, $code, UserPasswordEncoderInterface $passwordEncoder, Validation $validation)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(array('token' => $token));
        if(!$user){ return $this->redirectToRoute('app_login'); }

        // If code invalide
        if($user->getRenouvCode() != $code){
            return $this->render('root/app/pages/security/reinit_expired.html.twig', ['message' => 'Le lien n\'est pas valide ou a expir??.']);
        }

        // Form submitted
        if($request->isMethod('POST')){
            $data = json_decode($request->getContent());
            $password = $data->password->value;

            // validate password not empty and equal
            $resultat = $validation->validatePassword($password, $data->password2->value);      
            if($resultat != 1){
                return new JsonResponse(['code' => 2, 'errors' => [ 'error' => $resultat, 'success' => '' ]]);
            }

            $user = $this->setUserData($user, $password, $passwordEncoder);

            $url = $this->generateUrl('app_login', array(), UrlGeneratorInterface::ABSOLUTE_URL);
            return new JsonResponse(['code' => 1, 'message' => 'Le mot de passe a ??t?? renouvell??. La page va se rafraichir automatiquement.', 'url' => $url]);
        }
        return $this->render('root/app/pages/security/reinit.html.twig', ['token' => $token, 'code' => $code, 'type' => 'renouv']);
    }

    /**
     * @Route("/creation-mot-de-passe/{token}", name="app_password_unlock")
     */
    public function unlock(Request $request, $token, UserPasswordEncoderInterface $passwordEncoder, Validation $validation)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(array('token' => $token));
        if(!$user){ return $this->redirectToRoute('app_login'); }

        // If invalide
        if($user->getLastLogin() != null){
            return $this->render('root/app/pages/security/reinit_expired.html.twig', ['message' => 'Le lien n\'est plus valide car vous vous ??tes d??j?? connect??.']);
        }

        // Form submitted
        if($request->isMethod('POST')){
            $data = json_decode($request->getContent());
            $password = $data->password->value;

            // validate password not empty and equal
            $resultat = $validation->validatePassword($password, $data->password2->value);      
            if($resultat != 1){
                return new JsonResponse(['code' => 2, 'errors' => [ 'error' => $resultat, 'success' => '' ]]);
            }

            $user->setLastLogin(new DateTime());
            $user = $this->setUserData($user, $password, $passwordEncoder);

            $url = $this->generateUrl('app_login', array(), UrlGeneratorInterface::ABSOLUTE_URL);
            return new JsonResponse(['code' => 1, 'message' => 'Le mot de passe a ??t?? cr????. La page va se rafraichir automatiquement.', 'url' => $url]);
        }
        return $this->render('root/app/pages/security/reinit.html.twig', ['token' => $token, 'type' => 'unlock']);
    }

    private function setUserData(User $user, $password, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();

        $user->setPasswordCode(null);
        $user->setPasswordTime(null);
        $user->setRenouvCode(null);
        $user->setRenouvTime(new DateTime(date('Y-m-d', strtotime('+5 years'))));
        $user->setPassword($passwordEncoder->encodePassword($user, $password));
        $em->persist($user); $em->flush();

        return $user;
    }
}
