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
   *
   * @var \Drupal\webform\Entity\Webform
   */
  protected $domain;
  
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
      //
      if (!empty($form['webform']['elements']['domain'])) {
        // test1186.wb-horizon.kksa (test1186_wb_horizon_kksa)
        $key = $this->getCurrentDomain()->label() . ' (' . $this->getCurrentDomain()->id() . ')';
        $form['webform']['elements']['domain']['#default_value'] = $key;
        $form['webform']['elements']['domain']['#value'] = $key;
        // $form['webform']['elements']['domain']['#access'] = false;
        $form['webform']['elements']['domain']['#wrapper_attributes']['class'][] = 'd-none';
      }
      else {
        $this->messenger()->addError("Le champs domaine est requis");
        return [];
      }
      $form['title'] = $webform->label();
      $form['description'] = $webform->getDescription();
      //
      $form['webform']['#attached']['library'][] = 'wb_horizon_public/style';
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
  
  /**
   *
   * @param [] $webform_id
   * @param [] $submission_id
   * @return []
   */
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
      // On verifie que l'utilisateur a le droit d'acceder à la soumission.
      $datas = $submissionData->getData();
      if (!(!empty($datas['domain']) && $datas['domain'] == $this->getCurrentDomain()->id())) {
        $this->messenger()->addError("Vous n'avais pas le droit d'acceder à ce contenu");
        // return [];
      }
      // $view_builder =
      // $this->entityTypeManager()->getViewBuilder('webform');
      // $form['webform'] = $view_builder->view($webform);
      $form['title'] = $webform->label();
      $form['description'] = $webform->getDescription();
      //
      
      $form['webform'] = \Drupal::service('entity.form_builder')->getForm($submissionData, 'edit');
      //
      if (!empty($form['webform']['elements']['domain'])) {
        // dump($form['webform']['elements']);
        // $form['webform']['elements']['domain']['#default_value'] =
        // $this->getCurrentDomain();
        $form['webform']['elements']['domain']['#value'] = $this->getCurrentDomain()->label();
        // $form['webform']['elements']['domain']['#access'] = false;
      }
      else {
        $this->messenger()->addError("Le champs domaine est requis");
        return [];
      }
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
  
  /**
   *
   * @param integer $webform_id
   * @return string[][]|\Drupal\Core\GeneratedUrl[][]
   */
  protected function loadSubmissions($webform_id) {
    $sousmisions = [];
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
    $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    
    // $query =
    // \Drupal::entityQuery('webform_submission')->accessCheck(FALSE)->condition('webform_id',
    // $webform_id);
    // $query->condition('uid', \Drupal::currentUser()->id());
    // // $query->condition('data.domain', $this->getCurrentDomain()->id());
    // $query->sort('changed', 'DESC');
    
    // $result = $query->execute();
    if ($result)
      foreach ($result as $item) {
        $submission = \Drupal\webform\Entity\WebformSubmission::load($item['sid']);
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
