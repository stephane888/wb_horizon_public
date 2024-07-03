<?php

namespace Drupal\wb_horizon_public\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_stripe\Plugin\Commerce\PaymentGateway\Stripe;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the Stripe payment gateway.
 * L'objectif principal de ce module est de permettre de surchager les
 * paramettres de connections de stripe en functions de la valeurs definie par
 * l'utilisateur.
 *
 * @CommercePaymentGateway(
 *   id = "lesroidelareno_stripe_override",
 *   label = "Stripe(default) by lesroidelareno",
 *   display_label = "Payer la totalité",
 *   forms = {
 *     "add-payment-method" = "Drupal\wb_horizon_public\PluginForm\Stripe\PaymentMethodAddFormOverride",
 *   },
 *   payment_method_types = {"credit_card"},
 *   credit_card_types = {
 *     "amex", "dinersclub", "discover", "jcb", "maestro", "mastercard", "visa", "unionpay"
 *   },
 *   js_library = "commerce_stripe/form",
 *   requires_billing_information = FALSE,
 * )
 */
class stripeOverride extends Stripe {
  
  /**
   *
   * @var \Drupal\lesroidelareno\Entity\CommercePaymentConfig
   */
  protected $commerce_payment_config;
  
  /**
   * On charge la valeur des access en function du domaine.
   */
  private function updateConfigs() {
    if (\Drupal::moduleHandler()->moduleExists('lesroidelareno')) {
      $DirectAccessRoutes = [
        'entity.commerce_payment_gateway.collection',
        'entity.commerce_payment_gateway.edit_form'
      ];
      if (!in_array(\Drupal::routeMatch()->getRouteName(), $DirectAccessRoutes)) {
        // On pourrait mettre en cache par domaine.
        if (!$this->commerce_payment_config) {
          $datas = \Drupal::entityTypeManager()->getStorage("commerce_payment_config")->loadByProperties([
            'domain_id' => \Drupal\lesroidelareno\lesroidelareno::getCurrentDomainId(),
            'payment_plugin_id' => 'stripe_cart_by_domain'
          ]);
          if ($datas)
            $this->commerce_payment_config = reset($datas);
        }
        //
        if ($this->commerce_payment_config) {
          $this->configuration['publishable_key'] = $this->commerce_payment_config->getPublishableKey();
          $this->configuration['secret_key'] = $this->commerce_payment_config->getSecretKey();
          $this->configuration['mode'] = $this->commerce_payment_config->getMode();
        }
        else {
          $this->configuration['publishable_key'] = '';
          $this->configuration['secret_key'] = '';
          $this->messenger()->addError("Paramettres de vente non configurer..");
        }
      }
    }
  }
  
  /**
   * Re-initializes the SDK after the plugin is unserialized.
   */
  public function __wakeup() {
    $this->updateConfigs();
    parent::__wakeup();
    $this->init();
  }
  
  /**
   * Initializes the SDK.
   */
  protected function init() {
    $this->updateConfigs();
    parent::init();
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\PaymentGatewayBase::buildConfigurationForm()
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['display_label']['#access'] = TRUE;
    return $form;
  }
  
  public function getPublishableKey() {
    $this->updateConfigs();
    return parent::getPublishableKey();
  }
  
  public function getSecretKey() {
    $this->updateConfigs();
    return parent::getSecretKey();
  }
  
}









