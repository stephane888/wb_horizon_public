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
    if (!$this->cacheACPu) {
      /** @var \Drupal\domain\DomainNegotiatorInterface $domain_negotiator */
      $domain_negotiator = \Drupal::service('domain.negotiator');
      $current_domain = $domain_negotiator->getActiveDomain();
      $this->cacheACPu = $this->ApcuBackendFactory->get($current_domain . $this->pluginId);
    }
    return $this->cacheACPu;
  }
  
}