<?php
use Drupal\domain_access\DomainAccessManagerInterface;

/**
 * Implements hook_views_data_alter().
 */
function wb_horizon_public_views_data_alter(array &$data) {
  /**
   * Ajout du filtre de domain pour les produits.
   * on identifie notre filtre avec 'product_field_domain_access'.
   * la colonne est definie : 'field'
   * la requete est cree par 'id'
   */
  if (!empty($data['commerce_product__field_domain_access'])) {
    $data['commerce_product__field_domain_access']['product_field_domain_access'] = [
      'title' => t('Current domain'),
      'group' => t('Domain'),
      'filter' => [
        'field' => DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD . '_target_id',
        'id' => 'lesroidelareno_domain_filter',
        'title' => t('Available on current domain'),
        'help' => t('Filters out nodes available on current domain ( published to current domain or all affiliates ).')
      ]
    ];
  }
  
  /**
   * Ajoute le filtre dans les resulats de recherches de search control.
   */
  if (!empty($data['search_api_index_produits']['field_domain_access'])) {
    $data['search_api_index_produits']['product_field_domain_access'] = [
      'title' => t(' Current domain '),
      'group' => t(' Domain '),
      'filter' => [
        'field' => DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD,
        'id' => 'lesroidelareno_domain_filter',
        'title' => t('Available on current domain'),
        'help' => t('Filters out nodes available on current domain ( published to current domain or all affiliates ).')
      ]
    ];
  }
  
  /**
   * On ajoute le filtre pour les blocs personnalisé.
   */
  if (!empty($data['block_content__field_domain_access'])) {
    $data['block_content__field_domain_access']['product_field_domain_access'] = [
      'title' => t('Current domain'),
      'group' => t('Domain'),
      'filter' => [
        'field' => DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD . '_target_id',
        'id' => 'lesroidelareno_domain_filter',
        'title' => t(' Available on current domain '),
        'help' => t(' Filters out nodes available on current domain ( published to current domain or all affiliates ). ')
      ]
    ];
  }
  
  /**
   * On ajoute le filtre pour les blocs personnalisé.
   */
  if (!empty($data['blocks_contents__field_domain_access'])) {
    $data['blocks_contents__field_domain_access']['product_field_domain_access'] = [
      'title' => t('Current domain'),
      'group' => t('Domain'),
      'filter' => [
        'field' => DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD . '_target_id',
        'id' => 'lesroidelareno_domain_filter',
        'title' => t(' Available on current domain '),
        'help' => t(' Filters out nodes available on current domain ( published to current domain or all affiliates ). ')
      ]
    ];
  }
  
  /**
   * On ajoute le filtre pour cv_entity.
   */
  if (!empty($data['cv_entity__field_domain_access'])) {
    $data['cv_entity__field_domain_access']['field_domain_access'] = [
      'title' => t('Current domain'),
      'group' => t('Domain'),
      'filter' => [
        'field' => DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD . '_target_id',
        'id' => 'lesroidelareno_domain_filter',
        'title' => t(' Available on current domain '),
        'help' => t(' Filters out nodes available on current domain ( published to current domain or all affiliates ). ')
      ]
    ];
  }
  
  /**
   * On ajoute le filtre pour commerce_order.
   */
  if (!empty($data['commerce_order__field_domain_access'])) {
    $data['commerce_order__field_domain_access']['field_domain_access'] = [
      'title' => t('Current domain'),
      'group' => t('Domain'),
      'filter' => [
        'field' => DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD . '_target_id',
        'id' => 'lesroidelareno_domain_filter',
        'title' => t(' Available on current domain '),
        'help' => t(' Filters out nodes available on current domain ( published to current domain or all affiliates ). ')
      ]
    ];
  }
  
  /**
   * On ajoute le filtre pour commerce_payment.
   */
  if (!empty($data['commerce_payment__field_domain_access'])) {
    $data['commerce_payment__field_domain_access']['field_domain_access'] = [
      'title' => t('Current domain'),
      'group' => t('Domain'),
      'filter' => [
        'field' => DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD . '_target_id',
        'id' => 'lesroidelareno_domain_filter',
        'title' => t(' Available on current domain '),
        'help' => t(' Filters out nodes available on current domain ( published to current domain or all affiliates ). ')
      ]
    ];
  }
  
  /**
   * On ajoute le filtre pour commerce_payment_method.
   */
  if (!empty($data['commerce_payment_method__field_domain_access'])) {
    $data['commerce_payment_method__field_domain_access']['field_domain_access'] = [
      'title' => t('Current domain'),
      'group' => t('Domain'),
      'filter' => [
        'field' => DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD . '_target_id',
        'id' => 'lesroidelareno_domain_filter',
        'title' => t(' Available on current domain '),
        'help' => t(' Filters out nodes available on current domain ( published to current domain or all affiliates ). ')
      ]
    ];
  }
  
  /**
   * On ajoute le filtre pour commerce_promotion_coupon.
   */
  if (!empty($data['commerce_promotion_coupon__field_domain_access'])) {
    $data['commerce_promotion_coupon__field_domain_access']['field_domain_access'] = [
      'title' => t('Current domain'),
      'group' => t('Domain'),
      'filter' => [
        'field' => DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD . '_target_id',
        'id' => 'lesroidelareno_domain_filter',
        'title' => t(' Available on current domain '),
        'help' => t(' Filters out nodes available on current domain ( published to current domain or all affiliates ). ')
      ]
    ];
  }
  // commerce_promotion
  /**
   * On ajoute le filtre pour commerce_promotion.
   */
  if (!empty($data['commerce_promotion__field_domain_access'])) {
    $data['commerce_promotion__field_domain_access']['field_domain_access'] = [
      'title' => t('Current domain'),
      'group' => t('Domain'),
      'filter' => [
        'field' => DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD . '_target_id',
        'id' => 'lesroidelareno_domain_filter',
        'title' => t(' Available on current domain '),
        'help' => t(' Filters out nodes available on current domain ( published to current domain or all affiliates ). ')
      ]
    ];
  }
  
  // booking_reservation
  /**
   * On ajoute le filtre pour booking_reservation.
   */
  if (!empty($data['booking_reservation__field_domain_access'])) {
    $data['booking_reservation__field_domain_access']['field_domain_access'] = [
      'title' => t('Current domain'),
      'group' => t('Domain'),
      'filter' => [
        'field' => DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD . '_target_id',
        'id' => 'lesroidelareno_domain_filter',
        'title' => t(' Available on current domain '),
        'help' => t(' Filters out nodes available on current domain ( published to current domain or all affiliates ). ')
      ]
    ];
  }
  
  /**
   * On ajoute le filtre pour filtrer les differents modeles de themes.
   */
  if ($data['site_type_datas__terms']) {
    $data['site_type_datas__terms']['filter_theme_model'] = [
      'title' => t('Filtre de theme'),
      'group' => t('Filtre de theme'),
      'filter' => [
        'field' => 'terms_target_id',
        'id' => 'lesroidelareno_tag_filter',
        'title' => t(' Filtre de theme '),
        'help' => t(" Permet de filtrer les themes en function de l'id du terme taxonomie ")
      ]
    ];
  }
  /**
   * On ajoute le filtre par domaine pour les promotions.
   */
  if (!empty($data['commerce_product_field_data'])) {
    $data['commerce_product_field_data']['view_filter_promotion_product_promotion'] = [
      'title' => t('Promotion/discount'),
      'group' => t('Promotion by domain'),
      'filter' => [
        'field' => 'product_id',
        'id' => 'view_filter_promotion_by_domain',
        'title' => t('Product has promotion'),
        'help' => t(' allows you to filter products that may have a valid promotion')
      ]
    ];
  }
  /**
   * On ajoute les filtres dynamic ou un peu plus complexce.
   */
  foreach ($data as $table_name => $table_data) {
    if (str_contains($table_name, 'search_api_index_')) {
      /**
       * On ajoute le filtre par domaine pour les produits.
       */
      $data[$table_name]['wb_horizon_public_search_api_index'] = [
        'title' => t('Current domain'),
        'group' => t('Domain'),
        'filter' => [
          'field' => DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD, // valeur
                                                                         // au
                                                                         // niveau
                                                                         // de
                                                                         // table
                                                                         // search_api_db_product_mode.
          'id' => 'lesroidelareno_domain_filter',
          'title' => t(' Available on current domain '),
          'help' => t(' Filters out nodes available on current domain ( published to current domain or all affiliates ). ')
        ]
      ];
      /**
       * On ajoute le filtre par domaine pour les promotions.
       */
      $data[$table_name]['wb_horizon_public_search_api_index'] = [
        'title' => t('Promotion/discount'),
        'group' => t('Promotion by domain'),
        'filter' => [
          'field' => 'product_id',
          'id' => 'view_filter_promotionsearch_api_by_domain',
          'title' => t('Product has promotion'),
          'help' => t('Allows you to filter products that may have a valid promotion')
        ]
      ];
    }
    else
      foreach ($table_data as $field_name => $field_data) {
        // On se rassure que le fitre parent (more_fields_checkbox_list) est
        // disponible.
        if (!empty($field_data['filter']) && $field_data['filter']['id'] == 'more_fields_checkbox_list') {
          if (!empty($data[$table_name][$field_name]['filter']['field'])) {
            $mf = [
              'title' => t('Custom filter term taxonomy with current domain'),
              'group' => t('wb_horizon_public'),
              'filter' => $data[$table_name][$field_name]['filter']
            ];
            $mf['filter']['id'] = 'wb_horizon_public_term_filter';
            $data[$table_name]['wb_horizon_public_' . $data[$table_name][$field_name]['filter']['field']] = $mf;
          }
        }
      }
  }
}