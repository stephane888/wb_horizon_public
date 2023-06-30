<?php

namespace Drupal\wb_horizon_public\Plugin\views\filter;

use Drupal\more_fields\Plugin\views\filter\MoreFieldsCheckboxList;

/**
 * Permet de filtrer les entities en function du domaine encours.
 * Elle etant la methode de definie par DomainAccessCurrentAllFilter et modifier
 * la function de filtrer afin d'avoir un filtre complet.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("wb_horizon_public_term_filter")
 */
class CheckboxListWithDomain extends MoreFieldsCheckboxList {
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\more_fields\Plugin\views\filter\MoreFieldsCheckboxList::FilterCountEntitiesHasterm()
   */
  public function FilterCountEntitiesHasterm() {
    $queryEntity = parent::FilterCountEntitiesHasterm();
    // on va egalement filtrer en function du domaine encours.
    /** @var \Drupal\domain\DomainNegotiatorInterface $domain_negotiator */
    $domain_negotiator = \Drupal::service('domain.negotiator');
    $current_domain = $domain_negotiator->getActiveDomain();
    $queryEntity->condition(\Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD, $current_domain->id());
    return $queryEntity;
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\taxonomy\Plugin\views\filter\TaxonomyIndexTid::getCacheContexts()
   */
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();
    // Le resultat varie en function du domain.
    // @todo See https://www.drupal.org/node/2352175.
    $contexts[] = 'url.site';
    return $contexts;
  }
  
}