<?php

namespace Drupal\wb_horizon_public\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\domain\DomainNegotiatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LanguageLighterForm.
 */
class SubmissionFilterForm extends FormBase {

  /**
   *
   * @var \Drupal\domain\DomainNegotiatorInterface
   */
  protected $domainNegotiator;

  /**
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('domain.negotiator'),
      $container->get('entity_type.manager')
    );
  }

  /**
   *
   * @param DomainNegotiatorInterface $domainNegotiator
   * @param EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(
    DomainNegotiatorInterface $domainNegotiator,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->domainNegotiator = $domainNegotiator;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wb_horizon_public_submission_filter_form';
  }

  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /**
     * Building option for the selection of webform related 
     */
    $formStorage = $this->entityTypeManager->getStorage("webform");
    $options = [];
    /**
     * @var  \Drupal\webform\Entity\Webform[] $webforms
     */
    $webforms = $form_state->getBuildInfo()["args"][0];
    foreach ($webforms as $key => $webform) {
      $options[$webform->id()] = $webform->label();
    }


    $request = $this->getRequest();
    $form = [
      "#attributes" => [
        "class" => [
          // "form-items-inline"
        ]
      ],
      'webform' => [
        '#type' => 'select2',
        '#title' => $this->t('Webforms'),
        "#description" => $this->t("Leave empty to load submissions from all your webforms"),
        "#multiple" => true,
        '#options' => $options,
        '#minimumInputLength' => 3,
        "#default_value" => $request->query->get("webform") ?  explode("--", $request->query->get("webform")) : null
      ],
      'limit' => [
        '#type' => 'number',
        '#title' => $this->t('Number per page'),
        '#min' => 1, // Définir selon les besoins
        "#default_value" => $request->query->get("limit") ?? 10
      ],
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t(' Filtrer ')
      ]
    ];

    return $form;
  }

  /**
   * --
   *
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $request = $this->getRequest();
    $filter = [
      "webform" => implode("--", $form_state->getValue("webform")),
      "limit" => $form_state->getValue("limit"),
    ];
    // dd($filter);
    $form_state->setRedirect("<current>", array_merge($request->query->all(), $filter));
  }
}
