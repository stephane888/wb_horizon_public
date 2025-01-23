<?php
declare(strict_types = 1);

namespace Drupal\wb_horizon_public\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\wb_horizon_public\Services\CreateUpdatePage;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a wb-horizon public form.
 */
final class ManagePageMenusContentsForm extends FormBase {
  /**
   *
   * @var CreateUpdatePage
   */
  protected $CreateUpdatePage;
  
  function __construct(CreateUpdatePage $CreateUpdatePage) {
    $this->CreateUpdatePage = $CreateUpdatePage;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('wb_horizon_public.create_update_page'));
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'wb_horizon_public_manage_page_menus_contents';
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#attributes']['class'] = [
      'width-phone',
      'mx-auto',
      'my-5'
    ];
    $form['#attributes']['id'] = 'wb_horizon_public_manage_page_menus_contents';
    $form['action_to_do'] = [
      '#type' => 'radios',
      '#title' => $this->t('Select an action'),
      '#required' => TRUE,
      '#options' => [
        'add_menu' => $this->t('Add a menu')
        // 'edit_menu' => $this->t('Edit an existing menu')
      ],
      '#ajax' => [
        'callback' => self::class . '::ReloadMenu',
        'wrapper' => 'wb_horizon_public_manage_page_menus_contents',
        'effect' => 'fade'
      ]
    ];
    if ($action_to_do = $form_state->getValue('action_to_do')) {
      if ($action_to_do == 'add_menu')
        $this->elementsToAddNewMenu($form, $form_state);
    }
    
    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Apply')
      ]
    ];
    return $form;
  }
  
  protected function elementsToAddNewMenu(array &$form, FormStateInterface $form_state) {
    $form['menu_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Menu Name'),
      '#required' => true
    ];
    $form['content_to_display'] = [
      '#type' => 'select',
      '#title' => $this->t('content to display'),
      '#required' => true,
      '#options' => [
        '' => $this->t('Select'),
        'page' => 'Page de contenu',
        'produit' => 'Page de produits'
      ],
      '#ajax' => [
        'callback' => self::class . '::ReloadMenu',
        'wrapper' => 'wb_horizon_public_manage_page_menus_contents',
        'effect' => 'fade'
      ]
    ];
    if ($content_to_display = $form_state->getValue('content_to_display')) {
      if ($content_to_display == 'page') {
        $form['load_content'] = [
          '#type' => 'select',
          '#title' => $this->t('Selectionner le type de page à creer'),
          '#required' => true,
          '#options' => [
            '' => $this->t('Select'),
            'page_text' => 'Page de presentation text uniquement'
            // 'page_text_image' => 'Page de presentation text et image',
            // 'Page_custom' => 'Page personnaliser'
          ],
          '#ajax' => [
            'callback' => self::class . '::ReloadMenu',
            'wrapper' => 'wb_horizon_public_manage_page_menus_contents',
            'effect' => 'fade'
          ]
        ];
      }
      elseif ($content_to_display == 'produit') {
        $form['configure_view'] = [
          '#type' => 'details',
          '#open' => true,
          '#title' => t('Product sorting'),
          '#descritpion' => 'Configure how your products will be sorted',
          '#attributes' => [
            'id' => 'creation_site_virtuel_view_name_display_id'
          ],
          '#tree' => true
        ];
        // On peut ajouter des filtres supplementaire comme l'ordre de trie.
        $form['configure_view']['collections'] = [
          "#type" => "checkboxes",
          '#title' => $this->t("Select collections"),
          '#options' => $this->getListCollections(),
          '#required' => true,
          '#descritpion' => $this->t('Allows you to manage the display of products')
        ];
        $form['configure_view']['field_products'] = [
          '#type' => 'select2',
          '#title' => $this->t('Select products'),
          '#options' => [],
          '#autocomplete' => TRUE,
          '#target_type' => 'commerce_product',
          '#selection_handler' => 'default',
          '#multiple' => TRUE,
          '#description' => $this->t('Select the products that will be used when generating the model'),
          '#selection_settings' => [
            // 'target_bundles' => [
            // 'chapeau' => 'chapeau'
            // ],
            'sort' => [
              'field' => 'title',
              'direction' => 'ASC'
            ]
          ],
          '#select2' => [
            'minimumInputLength' => 2,
            'placeholder' => t('Enter the name of a product')
          ]
        ];
      }
    }
  }
  
  protected function getListCollections() {
    $entities = [];
    foreach (\Drupal::entityTypeManager()->getStorage("hbk_collection")->loadMultiple() as $key => $collection) {
      if ($collection->get('status')) {
        $entities[$key] = $collection->label();
      }
    }
    return $entities;
  }
  
  /**
   * Charge les types de produits.
   *
   * @return string[]|\Drupal\Core\StringTranslation\TranslatableMarkup[]|NULL[]
   */
  protected function getListProductsType() {
    $entities = [];
    foreach (\Drupal::entityTypeManager()->getStorage("commerce_product_type")->loadMultiple() as $key => $product_type) {
      if ($product_type->get('status')) {
        $entities[$key] = $product_type->label();
      }
    }
    return $entities;
  }
  
  static public function ReloadMenu(array &$form, FormStateInterface $form_state) {
    return $form;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $form_values = $form_state->getValues();
    $this->messenger()->addStatus($this->t('Page create'));
    /**
     * Donées provenant de l'entite, utile lors de la MAJ.
     *
     * @var array $values
     */
    $values = [];
    $this->CreateUpdatePage->createUpdatePage($values, $form_values);
  }
}
