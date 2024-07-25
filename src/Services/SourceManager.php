<?php

namespace Drupal\wb_horizon_public\Services;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManager;

class SourceManager extends ControllerBase {
    /**
     * Retrieve the the right booking_config_type
     * si lesroidelareno n'est pas installé alors il essaie de lire la configuration wb_horizon_public.config_auto_ecole
     * si cette config est absente alors il la crée
     */
    public function getEntityConfig($config_field = "domain_source_id", $config_id = "wb_horizon_public.source_site_configs") {
        $entity_type_id = "booking_config_type";
        $entityConfig = null;
        $hasLesroidelareno = \Drupal::moduleHandler()->moduleExists('lesroidelareno');
        if ($hasLesroidelareno) {
            $entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->load(\Drupal\lesroidelareno\lesroidelareno::getCurrentPrefixDomain());
        } else {
            /**
             *  @var \Drupal\Core\Config\Config $configs
             */
            $configs = \Drupal::service('config.factory')->getEditable($config_id);
            if ($configs) {
                $entityConfigid = $configs->get($config_field);
                if ($entityConfigid)
                    $entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->load($entityConfigid);
            }
        }
        if (!$entityConfig) {
            if ($hasLesroidelareno) {
                $entityConfigId = \Drupal\lesroidelareno\lesroidelareno::getCurrentPrefixDomain();
            } else {
                $entityConfigId = "booking_config_creneaux";
            }
            $values = [
                'label' => 'Configuration des créneaux.',
                'days' => \Drupal\booking_system\DaysSettingsInterface::DAYS,
                'id' => $entityConfigId
            ];
            $entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->create($values);
            $entityConfig->save();
            if (!$hasLesroidelareno) {
                /**
                 *  we update the configuration
                 *  @var \Drupal\Core\Config\Config $configs
                 */
                $configs = \Drupal::service('config.factory')->getEditable($config_id);
                $configs->set($config_field, $entityConfigId);
                $configs->save();
            }
        }
        return $entityConfig;
    }
}
