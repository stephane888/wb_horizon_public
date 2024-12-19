<?php

namespace Drupal\wb_horizon_public\Plugin\ManageModuleConfig;

use Drupal\manage_module_config\ManageModuleConfigPluginBase;
use Drupal\Core\Url;

/**
 * Gestion du menu.
 *
 * @ManageModuleConfig(
 *   id = "manage_pages_menu_content",
 *   label = @Translation("Gestion des pages"),
 *   description = @Translation("Foo description.")
 * )
 */
class ManagePagesMenuContent extends ManageModuleConfigPluginBase {
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageModuleConfigInterface::GetName()
   */
  public function GetName() {
    return $this->configuration['name'];
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageModuleConfigInterface::getRoute()
   */
  public function getRoute() {
    return Url::fromRoute('wb_horizon_public.manage_page_menus_contents_form', [], []);
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageModuleConfigInterface::getDescription()
   */
  public function getDescription() {
    return $this->configuration['description'];
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageModuleConfigPluginBase::defaultConfiguration()
   */
  public function defaultConfiguration() {
    return [
      'name' => t('Page and menu management'),
      'description' => t("Allows you to create pages, associate content with them and link to a menu"),
      'enable' => true,
      'icon_svg_class' => 'btn-wbu-background text-white btn-lg',
      'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="2rem" height="2rem" style="fill:currentColor;" viewBox="0 0 448 512"> <path d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z"/></svg>'
    ] + parent::defaultConfiguration();
  }
}
