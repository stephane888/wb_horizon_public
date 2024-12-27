<?php

namespace Drupal\wb_horizon_public\Plugin\views\filter;

use Drupal\view_filter_promotion\Plugin\views\filter\ViewFilterPromotionSearchApi;

/**
 * Permet de filtrer les entities en function du domaine encours.
 * Elle etant la methode de definie par DomainAccessCurrentAllFilter et modifier
 * la function de filtrer afin d'avoir un filtre complet.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("view_filter_promotionsearch_api_by_domain")
 */
class ViewFilterPromotionSearchApiByDomain extends ViewFilterPromotionSearchApi {
  
  /**
   *
   * @return \Drupal\Core\Cache\ApcuBackend
   */
  protected function getCacheACPu() {
    if (!$this->cacheDatas) {
      $id = $this->pluginId;
      /** @var \Drupal\domain\DomainNegotiatorInterface $domain_negotiator */
      $domain_negotiator = \Drupal::service('domain.negotiator');
      $current_domain = $domain_negotiator->getActiveDomain();
      if ($current_domain)
        $id = $current_domain->id() . $this->pluginId;
      if (function_exists('apcu_cache_info')) {
        $this->cacheDatas = $this->ApcuBackendFactory->get($id);
      }
      else {
        $this->cacheDatas = $this->DatabaseBackendFactory->get($id);
      }
    }
    return $this->cacheDatas;
  }
}