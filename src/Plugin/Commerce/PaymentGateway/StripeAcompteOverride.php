<?php

namespace Drupal\wb_horizon_public\Plugin\Commerce\PaymentGateway;

use Drupal\stripebyhabeuk\Plugin\Commerce\PaymentGateway\StripeAcompte;

/**
 * Provides the Stripe payment gateway.
 * L'objectif principal de ce module est de permettre de surchager les
 * paramettres de connections de stripe en functions de la valeurs definie par
 * l'utilisateur.
 *
 * @CommercePaymentGateway(
 *   id = "stripeacompteoverride",
 *   label = "StripeHabeuk Acompte by lesroidelareno",
 *   display_label = "Payer l'acompte",
 *   forms = {
 *     "add-payment-method" = "Drupal\wb_horizon_public\PluginForm\Stripe\PaymentMethodAddAcompteOverride",
 *   },
 *   payment_method_types = {"credit_card"},
 *   credit_card_types = {
 *     "amex", "dinersclub", "discover", "jcb", "maestro", "mastercard", "visa", "unionpay"
 *   },
 *   js_library = "commerce_stripe/form",
 *   requires_billing_information = FALSE,
 * )
 */
class StripeAcompteOverride extends StripeAcompte {
  /**
   * Permet de terminer si la configuration est deja à jour.
   *
   * @var string
   */
  private $configIsUpdate = FALSE;
  
  /**
   *
   * @var \Drupal\lesroidelareno\Entity\CommercePaymentConfig
   */
  protected $commerce_payment_config;
  
  /**
   * Re-initializes the SDK after the plugin is unserialized.
   */
  public function __wakeup() {
    parent::__wakeup();
    // new approche.
    $this->updateConfigs();
  }
  
  /**
   * On charge la valeur des access en function du domaine.
   */
  private function updateConfigs() {
    if (\Drupal::moduleHandler()->moduleExists('lesroidelareno')) {
      $DirectAccessRoutes = [
        'entity.commerce_payment_gateway.collection',
        'entity.commerce_payment_gateway.edit_form'
      ];
      if (!$this->configIsUpdate && !in_array(\Drupal::routeMatch()->getRouteName(), $DirectAccessRoutes)) {
        if (!$this->commerce_payment_config) {
          $datas = \Drupal::entityTypeManager()->getStorage("commerce_payment_config")->loadByProperties([
            'domain_id' => \Drupal\lesroidelareno\lesroidelareno::getCurrentDomainId(),
            'payment_plugin_id' => 'paiement_acompte'
          ]);
          if ($datas)
            $this->commerce_payment_config = reset($datas);
        }
        //
        if ($this->commerce_payment_config) {
          $this->configuration['publishable_key'] = $this->commerce_payment_config->getPublishableKey();
          $this->configuration['secret_key'] = $this->commerce_payment_config->getSecretKey();
          $this->configuration['mode'] = $this->commerce_payment_config->getMode();
          $this->configuration['percent_value'] = (int) $this->commerce_payment_config->getPercentValue();
          $this->configuration['min_value_paid'] = (int) $this->commerce_payment_config->getMinValuePaid();
          $this->configIsUpdate = true;
        }
        else {
          $this->configuration['publishable_key'] = '';
          $this->configuration['secret_key'] = '';
          $this->messenger()->addError("Paramettres de vente non configurer ...");
        }
      }
    }
  }
  
  // public function setConfiguration(array $configuration) {
  // $this->updateConfigs();
  // parent::setConfiguration($configuration);
  // }
  public function getPercentValue() {
    $this->updateConfigs();
    return parent::getPercentValue();
  }
  
  public function getMinValuePaid() {
    $this->updateConfigs();
    return parent::getMinValuePaid();
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