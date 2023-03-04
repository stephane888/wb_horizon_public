<?php

namespace Drupal\wb_horizon_public;

use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Core\Url;
use Drupal\blockscontent\Entity\BlocksContents;
use Drupal\commerce_product\Entity\Product;
use Drupal\node\Entity\Node;

/**
 * --
 *
 * @author stephane
 *        
 */
class WbHorizonPublic {
  
  /**
   *
   * @param BlocksContents $BlocksContents
   */
  static public function manageBlocksContents(BlocksContents $BlocksContents) {
    $link = '';
    if (!empty($BlocksContents->id()))
      $link = [
        '#type' => 'dropbutton',
        '#dropbutton_type' => 'small',
        '#links' => [
          'simple_form' => [
            'title' => t('Editer'),
            'url' => Url::fromRoute('entity.blocks_contents.edit_form', [
              'blocks_contents' => $BlocksContents->id()
            ]),
            '#options' => [
              'attributes' => [
                'target' => '_blank',
                'class' => []
              ]
            ]
          ],
          'demo' => [
            'title' => t('Traduction'),
            'url' => Url::fromRoute('entity.blocks_contents.content_translation_overview', [
              'blocks_contents' => $BlocksContents->id()
            ]),
            '#options' => [
              'attributes' => [
                'target' => '_blank',
                'class' => []
              ]
            ]
          ]
        ]
      ];
    return $link;
  }
  
  static public function getRouteToedit(Paragraph $Paragraph) {
    $link = '';
    if ($Paragraph->hasField('layout_builder__layout')) {
      $entity_type_id = 'paragraph';
      /**
       *
       * @var \Drupal\Core\Render\Renderer $renderer
       */
      $renderer = \Drupal::service('renderer');
      if (!empty($Paragraph->id()))
        $link = [
          '#type' => 'link',
          '#title' => t('Config layout'),
          '#url' => Url::fromRoute("layout_builder.overrides.$entity_type_id.view", [
            'paragraph' => $Paragraph->id()
          ]),
          '#options' => [
            'attributes' => [
              'target' => '_blank',
              'class' => []
            ]
          ]
        ];
      // $link = $renderer->renderRoot($link);
    }
    return $link;
  }
  
  /**
   *
   * @param Product $Product
   * @return string|string[]|\Drupal\Core\Url[]|string[][][]|array[][][]
   */
  static public function getRouteVariations(Product $Product) {
    $link = '';
    /**
     *
     * @var \Drupal\Core\Render\Renderer $renderer
     */
    $renderer = \Drupal::service('renderer');
    if (!empty($Product->id())) {
      $nbre = count($Product->getVariationIds());
      $error = false;
      if ($nbre)
        $nbre = 'Editer ( ' . $nbre . ' variations )';
      else {
        $nbre = 'Ajouter une variation ( ne serra pas affichage, ni dupliqué )';
        $error = true;
      }
      $link = [
        '#type' => 'link',
        '#title' => $nbre,
        '#url' => Url::fromRoute("entity.commerce_product_variation.collection", [
          'commerce_product' => $Product->id()
        ]),
        '#options' => [
          'attributes' => [
            'target' => '_blank',
            'class' => [],
            'style' => $error ? 'color:#f00;' : '',
            'title' => $error ? ' Vous devez ajouter au moins une variation pour que votre produit soit valide ' : ' Votre produit est valide '
          ]
        ]
      ];
    }
    return $link;
  }
  
  /**
   *
   * @param Node $node
   * @return string[]
   */
  static public function manageNode(Node $node) {
    $link = '';
    if (!empty($node->id()))
      $link = [
        '#type' => 'dropbutton',
        '#dropbutton_type' => 'small',
        '#links' => [
          'simple_form' => [
            'title' => t('Editer'),
            'url' => Url::fromRoute('entity.node.edit_form', [
              'node' => $node->id()
            ]),
            '#options' => [
              'attributes' => [
                'target' => '_blank',
                'class' => []
              ]
            ]
          ],
          'demo' => [
            'title' => t('Traduction'),
            'url' => Url::fromRoute('entity.node.content_translation_overview', [
              'node' => $node->id()
            ]),
            '#options' => [
              'attributes' => [
                'target' => '_blank',
                'class' => []
              ]
            ]
          ]
        ]
      ];
    return $link;
  }
  
}