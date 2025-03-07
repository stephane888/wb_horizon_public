<?php
declare(strict_types = 1);

namespace Drupal\wb_horizon_public\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Stephane888\Debug\Repositories\ConfigDrupal;

/**
 * Returns responses for wb-horizon public routes.
 */
final class ListUserWbfController extends ControllerBase {
  
  /**
   * Builds the response.
   */
  public function __invoke() {
    $forms = [];
    $form_ids = ConfigDrupal::config("manage_module_config.webformsusers");
    if (!empty($form_ids['webforms_users']))
      foreach ($form_ids['webforms_users'] as $webform_id) {
        /**
         *
         * @var \Drupal\webform\Entity\Webform $webform
         */
        $webform = $this->entityTypeManager()->getStorage('webform')->load($webform_id);
        if ($webform) {
          /**
           *
           * @var \Drupal\webform\WebformSubmissionStorage $submission_storage
           */
          $submission_storage = \Drupal::entityTypeManager()->getStorage('webform_submission');
          $view_builder = $this->entityTypeManager()->getViewBuilder('webform');
          $form['webform'] = $view_builder->view($webform);
          $form['title'] = $webform->label();
          $form['description'] = $webform->getDescription();
          $form['number'] = $submission_storage->getTotal($webform);
          $form['url'] = Url::fromRoute('wb_horizon_public.edit_webform', [
            'webform_id' => $webform_id
          ])->toString();
          $forms[] = $form;
        }
      }
    
    return [
      '#theme' => 'wb_horizon_public_list_user_wbf',
      '#forms' => $forms
    ];
  }
}
