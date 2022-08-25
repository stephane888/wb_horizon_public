<?php

namespace Drupal\vuejs_entity\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\menu_item_extras\Entity\MenuItemExtrasMenuLinkContent;
use Drupal\Core\Entity\Exception\UndefinedLinkTemplateException;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\system\Entity\Menu;
use Drupal\system\Plugin\Block\SystemMenuBlock;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'entity reference label' formatter.
 *
 * @FieldFormatter(
 *   id = "menu_vuejs_entity_field",
 *   label = @Translation("Affiche les elements du menu"),
 *   description = @Translation("Display the label of the referenced entities."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class MenuVuejsEntityFormatter extends EntityReferenceFormatterBase {
  /**
   * The menu link tree service.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected $menuTree;
  
  /**
   * The active menu trail service.
   *
   * @var \Drupal\Core\Menu\MenuActiveTrailInterface
   */
  protected $menuActiveTrail;
  
  /**
   *
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [] + parent::defaultSettings();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->menuTree = $container->get('menu.link_tree');
    $instance->menuActiveTrail = $container->get('menu.active_trail');
    return $instance;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];
    
    return $elements;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      /**
       *
       * @var Menu $entity
       */
      if ($entity->getEntityTypeId() != 'menu') {
        \Drupal::messenger()->addWarning(" Le type d'entité doit etre 'menu' ");
      }
      else {
        $menu_name = $entity->id();
        $parameters = $this->menuTree->getCurrentRouteMenuTreeParameters($menu_name);
        $parameters->setMinDepth(0);
        $tree = $this->menuTree->load($menu_name, $parameters);
        $manipulators = array(
          array(
            'callable' => 'menu.default_tree_manipulators:checkAccess'
          ),
          array(
            'callable' => 'menu.default_tree_manipulators:generateIndexAndSort'
          )
        );
        $tree = $this->menuTree->transform($tree, $manipulators);
        $elements[$delta] = $this->menuTree->build($tree);
      }
      // $elements[$delta]['#cache']['tags'] = $entity->getCacheTags();
    }
    
    return $elements;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity) {
    return $entity->access('view label', NULL, TRUE);
  }
  
}