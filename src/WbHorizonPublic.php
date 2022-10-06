<?php

namespace Drupal\wb_horizon_public;

use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Core\Url;

/**
 * --
 *
 * @author stephane
 *
 */
class WbHorizonPublic {

  static public function getRouteToedit(Paragraph $Paragraph) {
    $entity_type_id = 'paragraph';
    $link = '';
    if ($Paragraph->hasField('layout_builder__layout')) {
      /**
       *
       * @var \Drupal\Core\Render\Renderer $renderer
       */
      $renderer = \Drupal::service('renderer');
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
      $link = $renderer->renderRoot($link);
    }
    return $link;
  }

}