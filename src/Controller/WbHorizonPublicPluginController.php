<?php

namespace Drupal\wb_horizon_public\Controller;

use Symfony\Component\HttpFoundation\Request;
use Drupal\lesroidelareno\lesroidelareno;
use Drupal\booking_system\Controller\BookingSystemUseApp;

/**
 * Ce controlleur permet de fournir les routes pour l'application de
 * resrvation de creneau ( application de base ).
 *
 * @author stephane
 *        
 */
class WbHorizonPublicPluginController extends BookingSystemUseApp {
    protected static $default_id = 'wb_horizon_com';


    /**
     * Permet de generer et de configurer RDV par domaine.
     */
    public function ConfigureDefaultRDV() {
        $entity_type_id = "booking_config_type";
        $id = lesroidelareno::getCurrentPrefixDomain();
        if (!$id) {
            /**
             * Pour la configuration par defaut.
             */
            $id = self::$default_id;
        }
        $entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->load($id);
        if (!$entityConfig) {
            $entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->create([
                'id' => $id,
                'label' => 'Configuration des creneaux',
                'days' => \Drupal\booking_system\DaysSettingsInterface::DAYS
            ]);
            $entityConfig->save();
        }

        // dd($entityConfig->toArray());
        // $entityConfig->save();

        $form = $this->entityFormBuilder()->getForm($entityConfig, "edit", [
            'redirect_route' => 'bookingsystem_autoecole.config_resume',
            'booking_config_type_id' => $entityConfig->id()
        ]);
        return $form;
    }
}
