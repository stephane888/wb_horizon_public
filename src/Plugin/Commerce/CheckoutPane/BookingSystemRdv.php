<?php

namespace Drupal\wb_horizon_public\Plugin\Commerce\CheckoutPane;

use Drupal\commerce\InlineFormManager;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\Core\Url;

/**
 * Provides the billing information pane.
 *
 * @CommerceCheckoutPane(
 *   id = "booking_system_rdv",
 *   label = @Translation("Booking System RDV (defautl)"),
 *   default_step = "reservation_bk",
 *   wrapper_element = "fieldset",
 * )
 */
class BookingSystemRdv extends CheckoutPaneBase implements CheckoutPaneInterface {
  protected static $default_id = 'wb_horizon_com';

  /**
   * L'etape qui permet de selectionner le creneau.
   *
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $pane_form['html_reservation'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => 'Veuillez selectionner une date et un creneau'
    ];
    /**
     * @var \Drupal\Core\Entity\EntityInterface $entityConfig
     */
    $entityConfig = \Drupal::service('wb_horizon_public.source_manager')->getEntityConfig();

    $urlCalendar = Url::fromRoute("wb_horizon_public.booking_system.app_load_config_calendar");
    $urlCreneaux = Url::fromRoute("wb_horizon_public.booking_system.app_load_creneaux", [
      'booking_config_type_id' => $entityConfig->id(),
      'date' => null
    ]);
    $urlSave = Url::fromRoute("wb_horizon_public.booking_system.save_reservation", [
      'booking_config_type_id' => $entityConfig->id()
    ]);
    $pane_form['content_form'] = [
      '#type' => 'html_tag',
      '#tag' => 'section',
      "#attributes" => [
        'id' => 'app',
        'data-url-calendar' => '/' . $urlCalendar->getInternalPath(),
        'data-url-creneaux' => '/' . $urlCreneaux->getInternalPath(),
        'data-url-save' => '/' . $urlSave->getInternalPath(),
        'class' => [
          'my-5'
        ]
      ]
    ];
    $pane_form['content_form']['#attached']['library'][] = 'booking_system/booking_system_app2_checkout';

    // $this->checkoutFlow->submitForm($form, $form_state);
    return $pane_form;
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase::buildConfigurationForm()
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    return $form;
  }

  // On desactive cela, car le module ne le supporte pas pour l'instant.
  // public function buildPaneSummary() {
  // $summary = parent::buildPaneSummary();
  // $summary['html_reservation'] = [
  // '#type' => 'html_tag',
  // '#tag' => 'div',
  // '#value' => 'Formulaire de reservation'
  // ];
  // return $summary;
  // }
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    //
    parent::submitPaneForm($pane_form, $form_state, $complete_form);
  }
}
