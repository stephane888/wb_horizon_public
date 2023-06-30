<?php

namespace Drupal\wb_horizon_public\Plugin\views\filter;

use Drupal\domain_access\Plugin\views\filter\DomainAccessCurrentAllFilter;

/**
 * Permet de filtrer les entities en function du domaine encours.
 * Elle etant la methode de definie par DomainAccessCurrentAllFilter et modifier
 * la function de filtrer afin d'avoir un filtre complet.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("lesroidelareno_domain_filter")
 */
class lesroidelarenoDomainFilter extends DomainAccessCurrentAllFilter {
  
  /**
   *
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();
    $real_field = $this->tableAlias . '.' . $this->realField;
    /** @var \Drupal\domain\DomainNegotiatorInterface $domain_negotiator */
    $domain_negotiator = \Drupal::service('domain.negotiator');
    $current_domain = $domain_negotiator->getActiveDomain();
    $current_domain_id = $current_domain->id();
    //
    if (!empty($this->value)) {
      $this->query->addWhere('OR', $real_field, $current_domain_id, '=');
    }
  }
  
}