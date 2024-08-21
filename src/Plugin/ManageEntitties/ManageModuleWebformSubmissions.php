<?php

namespace Drupal\wb_horizon_public\Plugin\ManageEntitties;

use Drupal\manage_module_config\ManageEntitties\ManageEntittiesPluginBase;
use Drupal\lesroidelareno\lesroidelareno;
use Drupal\Core\Url;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Plugin implementation of the manage_entitties.
 *
 * @ManageEntitties(
 *   id = "manage_module_webform_submissions",
 *   label = @Translation("Manage Module webform submissions"),
 *   description = @Translation("Foo description."),
 *   entities = {
 *     "webform_submission"
 *   }
 * )
 */
class ManageModuleWebformSubmissions extends ManageEntittiesPluginBase {

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageEntitties\ManageEntittiesInterface::GetName()
   */
  public function GetName() {
    return $this->configuration['name'];
  }

  public function buildCollections(array &$datas) {
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageEntitties\ManageEntittiesInterface::getBaseRoute()
   */
  public function getBaseRoute() {
    return Url::fromRoute(
      'wb_horizon_public.webform_submission',
      [],
      []
    );
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageEntitties\ManageEntittiesInterface::getNumbers()
   */
  public function getNumbers() {
    $formStorage = \Drupal::entityTypeManager()->getStorage("webform");
    $webforms = (\Drupal::moduleHandler()->moduleExists('lesroidelareno')) ?
      $formStorage->loadByProperties(
        [
          "third_party_settings.webform_domain_access.field_domain_access" => \Drupal\lesroidelareno\lesroidelareno::getCurrentDomainId(),
        ]
      ) : $formStorage->loadMultiple();
    $query = \Drupal::database()->select("webform_submission", "submt");
    $query->fields("submt", ["sid"]);
    $or = $query->orConditionGroup();

    foreach ($webforms as $id => $webform) {
      $or->condition("webform_id", $webform->id());
    }

    $query->condition($or);
    return (int) $query->countQuery()->execute()->fetchField();
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageEntitties\ManageEntittiesInterface::getDescription()
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
      'name' => $this->t('Webform submissions'),
      'description' => $this->t("list of all your webform submissions"),
      'icon_svg' => '<svg width="1em" height="1em" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 315 315" xml:space="preserve"><path d="M280.5 153.5c0-4.143-3.024-7.5-7.167-7.5H248.5V7.5c0-4.143-3.024-7.5-7.167-7.5h-167C70.19 0 66.5 3.357 66.5 7.5V146H42.333c-4.143 0-7.833 3.357-7.833 7.5v154c0 4.143 3.69 7.5 7.833 7.5h231c4.143 0 7.167-3.357 7.167-7.5zm-32 7.5h4.562l-4.562 3.699zM81.5 15h152v162.816L228.881 182l-71.431 62.537L85.81 182l-4.31-3.66zm-15 146v4.224L62.004 161z"/><path d="M110.833 59h57c4.143 0 7.5-3.357 7.5-7.5s-3.357-7.5-7.5-7.5h-57c-4.143 0-7.5 3.357-7.5 7.5s3.357 7.5 7.5 7.5m-5.5 39.5c0 4.143 3.357 7.5 7.5 7.5h90c4.143 0 7.5-3.357 7.5-7.5s-3.357-7.5-7.5-7.5h-90a7.5 7.5 0 0 0-7.5 7.5m97.5 27.5h-90c-4.143 0-7.5 3.357-7.5 7.5s3.357 7.5 7.5 7.5h90c4.143 0 7.5-3.357 7.5-7.5s-3.357-7.5-7.5-7.5m0 34h-90c-4.143 0-7.5 3.357-7.5 7.5s3.357 7.5 7.5 7.5h90c4.143 0 7.5-3.357 7.5-7.5s-3.357-7.5-7.5-7.5"/></svg>',
      'icon_svg_class' => 'btn-circle btn-wbu-secondary text-dark btn-lg',
      'enable' => true
    ] + parent::defaultConfiguration();
  }
}
