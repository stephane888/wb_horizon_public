<?php

namespace Drupal\wb_horizon_public\Plugin\ManageModuleConfig;

use Drupal\manage_module_config\ManageModuleConfigPluginBase;
use Drupal\Core\Url;

/**
 * Gestion du menu.
 *
 * @ManageModuleConfig(
 *   id = "form_config",
 *   label = @Translation("Webforms configs"),
 *   description = @Translation("Configuration for webforms of the current domain.")
 * )
 */
class FormConfig extends ManageModuleConfigPluginBase
{

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageModuleConfigInterface::GetName()
   */
  public function GetName()
  {
    return $this->configuration['name'];
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageModuleConfigInterface::getRoute()
   */
  public function getRoute()
  {
    return Url::fromRoute(
      'wb_horizon_public.webform_config',
      [],
      [
        'query' => [
          'destination' => \Drupal::service('path.current')->getPath()
        ]
      ]
    );
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageModuleConfigInterface::getDescription()
   */
  public function getDescription()
  {
    return $this->configuration['description'];
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageModuleConfigPluginBase::defaultConfiguration()
   */
  public function defaultConfiguration()
  {
    return [
      'name' => 'Configuration des Formulaires',
      'description' => "Configuration for webforms of the current domain.",
      'enable' => true,
      'icon_svg_class' => 'text-white btn-lg bg-dark',
      'icon_svg' => '<svg fill="currentColor" width="1em" height="1em" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22 6H2a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h20a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1m0 4H2V7h20zh.001M22 17H2a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h20a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1m0 4H2v-3h20zh.001M10 14v1H2v-1zM2 3h8v1H2z"/><path fill="none" d="M0 0h24v24H0z"/></svg>'
    ] + parent::defaultConfiguration();
  }
}
