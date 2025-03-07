<?php
declare(strict_types = 1);

namespace Drupal\wb_horizon_public\Controller;

use Drupal\Core\Url;
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
        'description' => '',
        'sousmisions' => [],
        'add_submision' => NULL
      ];
      // On charge les sousmissions.
      if ($webform->hasSubmissions()) {
        $form['sousmisions'] = $this->loadSubmissions($webform_id);
      }
      $view_builder = $this->entityTypeManager()->getViewBuilder('webform');
      $form['webform'] = $view_builder->view($webform);
      $form['title'] = $webform->label();
      $form['description'] = $webform->getDescription();
      //
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
  
  public function editWebform($webform_id, $submission_id) {
    /**
     *
     * @var \Drupal\webform\Entity\Webform $webform
     */
    $webform = $this->entityTypeManager()->getStorage('webform')->load($webform_id);
    if ($webform) {
      $form = [
        'webform' => '',
        'title' => '',
        'description' => '',
        'sousmisions' => [],
        'add_submision' => NULL
      ];
      $form['sousmisions'] = $this->loadSubmissions($webform_id);
      $form['add_submision'] = [
        'url' => Url::fromRoute('wb_horizon_public.edit_webform', [
          'webform_id' => $webform_id
        ])->toString()
      ];
      $submissionData = \Drupal\webform\Entity\WebformSubmission::load($submission_id);
      //
      // $view_builder = $this->entityTypeManager()->getViewBuilder('webform');
      // $form['webform'] = $view_builder->view($webform);
      $form['title'] = $webform->label();
      $form['description'] = $webform->getDescription();
      //
      $form['webform'] = \Drupal::service('entity.form_builder')->getForm($submissionData, 'edit');
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
  
  protected function loadSubmissions($webform_id) {
    $sousmisions = [];
    $query = \Drupal::entityQuery('webform_submission')->accessCheck(FALSE)->condition('webform_id', $webform_id);
    $query->condition('uid', \Drupal::currentUser()->id());
    $query->sort('changed', 'DESC');
    $result = $query->execute();
    foreach ($result as $item) {
      $submission = \Drupal\webform\Entity\WebformSubmission::load($item);
      $data = $submission->getData();
      $titre = 'Libelle';
      if (!empty($data['titre']))
        $titre = $data['titre'];
      //
      $sousmisions[] = [
        'titre' => $titre,
        'url' => Url::fromRoute('wb_horizon_public.edit_webform_by_submission_id', [
          'webform_id' => $webform_id,
          'submission_id' => $submission->id()
        ])->toString()
      ];
    }
    return $sousmisions;
  }
}
