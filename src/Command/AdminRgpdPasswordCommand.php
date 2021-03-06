<?php

namespace App\Command;

use App\Entity\User;
use App\Service\CheckTime;
use App\Service\Mailer;
use App\Service\SettingsService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class AdminRgpdPasswordCommand extends Command
{
    protected static $defaultName = 'admin:rgpd:password';
    protected $em;
    protected $checkTime;
    protected $mailer;
    protected $router;
    protected $settingsService;

    public function __construct(EntityManagerInterface $em, CheckTime $checkTime, Mailer $mailer, RouterInterface $router, SettingsService $settingsService)
    {
        parent::__construct();

        $this->em = $em;
        $this->checkTime = $checkTime;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->settingsService = $settingsService;
    }
    
    protected function configure()
    {
        $this
            ->setDescription('Check if an user has to renew password because > 5 years.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $compteur = 0;
        $users = $this->em->getRepository(User::class)->findAll();
        foreach($users as $user){
            $resultat = $this->checkTime->lessThanToday($user->getRenouvTime());
            if($resultat == 1){
                $compteur++;

                //send mail to informe use to renew his password
                $code = uniqid();
                $user->setRenouvCode($code);
                $user->setRenouvTime(new DateTime());

                $this->em->persist($user); $this->em->flush();
                $url = $this->router->generate('app_password_renouv', ['token' => $user->getToken(), 'code' => $code], UrlGeneratorInterface::ABSOLUTE_URL);     
                if($this->mailer->sendMail(
                    'Renouvellement du mot de passe pour le site ' . $this->settingsService->getWebsiteName(),
                    'Renouvellement du mot de passe de votre compte',
                    'root/super/email/security/renouv.html.twig',
                    ['url' => $url, 'user' => $user, 'settings' => $this->settingsService->getSettings()],
                    $user->getEmail()
                ) != true){
                    $io->error('Erreur dans l\'envoie du mail.');
                };
            }
        }

        $io->text('Nombre d\'utilisateurs concern??s : ' . $compteur);
        $io->comment('--- [FIN DE LA COMMANDE] ---');

        return 0;
    }
}
