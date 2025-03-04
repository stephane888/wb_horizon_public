<?php
declare(strict_types = 1);

namespace Drupal\wb_horizon_public\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for wb-horizon public routes.
 */
final class EditWebformController extends ControllerBase {
  
  /**
   * Permet de mofifier un formulaire webform.
   */
  public function __invoke($webform_id) {
    /**
     *
     * @var \Drupal\webform\Entity\Webform $webform
     */
    $webform = $this->entityTypeManager()->getStorage('webform')->load($webform_id);
    if ($webform) {
      $form = [
        'webform' => '',
        'title' => '',
        'description' => ''
      ];
      
      $view_builder = $this->entityTypeManager()->getViewBuilder('webform');
      $form['webform'] = $view_builder->view($webform);
      $form['title'] = $webform->label();
      $form['description'] = $webform->getDescription();
      
      return [
        '#theme' => 'wb_horizon_public_edit_webform',
        '#form' => $form,
        '#attributes' => new \Drupal\Core\Template\Attribute([
          'id' => 'edit-webform--' . $webform->id(),
          'class' => [
            'container',
            'user-webform'
          ]
        ])
      ];
    }
    return [];
  }
}
