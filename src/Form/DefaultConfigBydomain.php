<?php

namespace Drupal\wb_horizon_public\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Stephane888\Debug\Repositories\ConfigDrupal;

/**
 * Class DefaultConfigBydomain.
 */
class DefaultConfigBydomain extends ConfigFormBase {
  
  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'wb_horizon_public.defaultconfigbydomain'
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'default_config_bydomain';
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $configs = ConfigDrupal::config('wb_horizon_public.defaultconfigbydomain');
    $form['commerce'] = [
      '#type' => 'details',
      '#title' => 'Commerce configs',
      '#open' => false,
      '#tree' => true
    ];
    $form['commerce']['texte_add_to_cart'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Texte &#039;add to cart&#039;'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => !empty($configs['commerce']['texte_add_to_cart']) ? $configs['commerce']['texte_add_to_cart'] : 'Add to cart'
    ];
    $form['commerce']['checkout_button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Texte "Passer la commande"'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => !empty($configs['commerce']['checkout_button_text']) ? $configs['commerce']['checkout_button_text'] : 'To order'
    ];
    $form['commerce']['cart_button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Texte "Voir le panier"'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => !empty($configs['commerce']['cart_button_text']) ? $configs['commerce']['cart_button_text'] : 'See cart'
    ];
    return parent::buildForm($form, $form_state);
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $config = $this->config('wb_horizon_public.defaultconfigbydomain');
    $config->set('commerce', $form_state->getValue('commerce'));
    $config->save();
  }
  
}
