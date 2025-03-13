<?php
declare(strict_types = 1);

namespace Drupal\wb_horizon_public\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Stephane888\Debug\Repositories\ConfigDrupal;
use Drupal\manage_module_config\ManageModuleConfig;

/**
 * Returns responses for wb-horizon public routes.
 */
final class ListUserWbfController extends ControllerBase {
  /**
   *
   * @var \Drupal\webform\Entity\Webform
   */
  protected $domain;
  
  /**
   * Builds the response.
   */
  public function __invoke() {
    $forms = [];
    $form_ids = ConfigDrupal::config("manage_module_config.webformsusers");
    //
    if (!empty($form_ids['webforms_users']))
      foreach ($form_ids['webforms_users'] as $webform_id) {
        if ($webform_id) {
          /**
           *
           * @var \Drupal\webform\Entity\Webform $webform
           */
          $webform = $this->entityTypeManager()->getStorage('webform')->load($webform_id);
          if ($webform) {
            $view_builder = $this->entityTypeManager()->getViewBuilder('webform');
            $form['webform'] = $view_builder->view($webform);
            $form['title'] = $webform->label();
            $form['description'] = $webform->getDescription();
            $form['number'] = $this->countSubmissions($webform_id);
            $form['url'] = Url::fromRoute('wb_horizon_public.edit_webform', [
              'webform_id' => $webform_id
            ])->toString();
            $forms[] = $form;
          }
        }
      }
    //
    // $form['#attached']['library'][] = 'wb_horizon_public/style';
    return [
      '#theme' => 'wb_horizon_public_list_user_wbf',
      '#forms' => $forms,
      '#attached' => [
        'library' => [
          'wb_horizon_public/style'
        ]
      ]
    ];
  }
  
  /**
   *
   * @param integer $webform_id
   * @return string[][]|\Drupal\Core\GeneratedUrl[][]
   */
  protected function countSubmissions($webform_id) {
    /**
     *
     * @var \Drupal\Core\Database\Connection $connexion
     */
    $connexion = \Drupal::database();
    $query = $connexion->select('webform_submission', 'ws');
    $query->fields("ws", [
      'sid'
    ]);
    $query->addJoin('INNER', 'webform_submission_data', 'wsd', 'ws.sid=wsd.sid');
    $query->condition('wsd.name', 'domain');
    $query->condition('wsd.value', $this->getCurrentDomain()->id());
    $query->condition('ws.webform_id', $webform_id);
    return $query->countQuery()->execute()->fetchField();
  }
  
  /**
   *
   * @return \Drupal\domain\Entity\Domain
   */
  protected function getCurrentDomain() {
    if (!$this->domain) {
      /**
       *
       * @var \Drupal\domain_source\HttpKernel\DomainSourcePathProcessor $domain_source
       */
      $domain_source = \Drupal::service('domain_source.path_processor');
      $this->domain = $domain_source->getActiveDomain();
    }
    return $this->domain;
  }
}
