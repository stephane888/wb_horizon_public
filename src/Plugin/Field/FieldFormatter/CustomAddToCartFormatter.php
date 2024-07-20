<?php

namespace Drupal\wb_horizon_public\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_product\Plugin\Field\FieldFormatter\AddToCartFormatter;
use Stephane888\Debug\Repositories\ConfigDrupal;

/**
 * Plugin implementation of the 'commerce_add_to_cart' formatter.
 *
 * @FieldFormatter(
 *   id = "custom_commerce_add_to_cart",
 *   label = @Translation("Custom Add to cart form (wb_horizon_public)"),
 *   field_types = {
 *     "entity_reference",
 *   },
 * )
 */
class CustomAddToCartFormatter extends AddToCartFormatter {
  
  /**
   *
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    // dump($elements);
    $configs = ConfigDrupal::config('wb_horizon_public.defaultconfigbydomain');
    // update value "add to cart"
    if (isset($elements[0]['add_to_cart_form'][0]['#value']) && !empty($configs['commerce']['texte_add_to_cart'])) {
      $elements[0]['add_to_cart_form'][0]['#value'] = $configs['commerce']['texte_add_to_cart'];
    }
    return $elements;
  }
  
}