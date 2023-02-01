<?php

namespace Drupal\wb_horizon_public\Plugin\Commerce\ShippingMethod;

use Drupal\commerce_shipping\Plugin\Commerce\ShippingMethod\ShippingMethodBase;
use Drupal\commerce_shipping\Entity\ShipmentInterface;
use Drupal\commerce_shipping\ShippingRate;
use Drupal\commerce_shipping\ShippingService;
use Drupal\commerce_shipping\PackageTypeManagerInterface;
use Drupal\state_machine\WorkflowManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_price\Price;

/**
 * Permet de mettre en place une methode de livraison gratuite qui evolura en
 * fonction des besoins, i.e elle s'affichera en function des configurations ou
 * conditions.
 *
 * @see https://www.flocondetoile.fr/blog/create-shipping-method-drupal-commerce-2
 *      pour example de methode d'expedition.
 * @see https://www.webomelette.com/how-create-drupal-commerce-shipment-promotion-offer
 *      pour une offre de promotion sur la livraison.
 * @see https://www.studiopresent.com/blog/drupal-commerce-2x-and-shipping-conditions
 *      pour ajouter des conditions d'expeditions.( Cela permet d'afficher
 *      uniquement les methodes qui remplissent les contraintes ).
 *     
 *     
 * @CommerceShippingMethod(
 *   id = "free_shipping_wb_horizon",
 *   label = @Translation("Free Shipping - Wb-Horizon"),
 * )
 */
class FreeShippingWbHorizon extends ShippingMethodBase {
  
  /**
   * Constructs a new FlatRate object.
   *
   * @param array $configuration
   *        A configuration array containing information about the plugin
   *        instance.
   * @param string $plugin_id
   *        The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *        The plugin implementation definition.
   * @param \Drupal\commerce_shipping\PackageTypeManagerInterface $package_type_manager
   *        The package type manager.
   * @param \Drupal\state_machine\WorkflowManagerInterface $workflow_manager
   *        The workflow manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PackageTypeManagerInterface $package_type_manager, WorkflowManagerInterface $workflow_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $package_type_manager, $workflow_manager);
    
    $this->services['default'] = new ShippingService('default', $this->configuration['rate_label']);
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'rate_label' => 'Free Shipping',
      'rate_description' => '',
      'services' => [
        'default'
      ]
    ] + parent::defaultConfiguration();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['rate_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Rate label'),
      '#description' => $this->t('Shown to customers when selecting the rate.'),
      '#default_value' => $this->configuration['rate_label'],
      '#required' => TRUE
    ];
    $form['rate_description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Rate description'),
      '#description' => $this->t('Provides additional details about the rate to the customer.'),
      '#default_value' => $this->configuration['rate_description']
    ];
    
    return $form;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['rate_label'] = $values['rate_label'];
      $this->configuration['rate_description'] = $values['rate_description'];
    }
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function calculateRates(ShipmentInterface $shipment) {
    $amount = new Price((string) '0', $shipment->getOrder()->getTotalPrice()->getCurrencyCode());
    $rates = [];
    $rates[] = new ShippingRate([
      'shipping_method_id' => $this->parentEntity->id(),
      'service' => $this->services['default'],
      'amount' => $amount,
      'description' => $this->configuration['rate_description']
    ]);
    return $rates;
  }
  
}