<?php

namespace App\Controller\Super;

use App\Entity\Settings;
use App\Service\SerializeData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/administrator/parametres", name="super_settings_")
 */
class SettingsController extends AbstractController
{
    const ATTRIBUTES_SETTINGS = ['id', 'websiteName', 'logo', 'emailGlobal', 'emailContact', 'emailRgpd', 'maxYear'];

    /**
     * @Route("/", options={"expose"=true}, name="edit")
     */
    public function edit(Request $request, SerializeData $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository(Settings::class)->findAll();

        if($request->isMethod('POST')){
            $settings = (count($settings) == 0) ? new Settings() : $settings[0];

            $data = json_decode($request->getContent());
            $settings->setWebsiteName($data->websiteName->value);
            $settings->setEmailGlobal($data->emailGlobal->value);
            $settings->setEmailContact($data->emailContact->value);
            $settings->setEmailRgpd($data->emailRgpd->value);
            $settings->setLogo($data->logo->value);
            $settings->setMaxYear($data->maxYear->value);
            $settings->setUrl($this->generateUrl('app_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL));
    
            $em->persist($settings); $em->flush();
            return new JsonResponse(['code' => 1]);
        }

        if(count($settings) == 0){
            return $this->render('root/super/pages/settings/edit.html.twig');
        }

        $settings = $serializer->getSerializeData($settings, self::ATTRIBUTES_SETTINGS);

        return $this->render('root/super/pages/settings/edit.html.twig', [
            'settings' => $settings
        ]);
    }
}
