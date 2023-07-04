<?php

namespace Drupal\wb_horizon_public\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Stephane888\Debug\Repositories\ConfigDrupal;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Http\RequestStack;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\domain\DomainNegotiator;

/**
 * Class DefaultConfigBydomain.
 */
class DefaultConfigBydomain extends ConfigFormBase implements ContainerInjectionInterface {
  
  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  
  /**
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;
  
  /**
   *
   * @var \Drupal\domain\DomainNegotiator
   */
  protected $DomainNegotiator;
  
  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *        The factory for configuration objects.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $EntityTypeManagerInterface, RequestStack $RequestStack, DomainNegotiator $DomainNegotiator) {
    parent::__construct($config_factory);
    $this->entityTypeManager = $EntityTypeManagerInterface;
    $this->request = $RequestStack->getCurrentRequest();
    $this->DomainNegotiator = $DomainNegotiator;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // $instance = parent::create($container);
    // $instance->entityTypeManager = $container->get('entity_type.manager');
    // $instance->request = $container->get('request_stack');
    // return $instance;
    return new static($container->get('config.factory'), $container->get('entity_type.manager'), $container->get('request_stack'), $container->get('domain.negotiator'));
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
    $query = $this->request->query->get('domain_config_ui_domain');
    if (empty($query)) {
      $domain = $this->DomainNegotiator->getActiveDomain();
      if ($domain) {
        $url = Url::fromRoute("wb_horizon_public.default_config_bydomain", [], [
          'query' => [
            'domain_config_ui_domain' => $domain->id(),
            'domain_config_ui_language' => ''
          ],
          'absolute' => TRUE
        ]);
        return new RedirectResponse($url->toString());
      }
    }
    
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
