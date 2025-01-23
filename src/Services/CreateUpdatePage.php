<?php
declare(strict_types = 1);

namespace Drupal\wb_horizon_public\Services;

use Drupal\Core\Config\StorageCacheInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\creation_site_virtuel\Entity\SiteInternetEntity;
use Drupal\creation_site_virtuel\Entity\SiteTypeDatas;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\apivuejs\Services\DuplicateEntityReference;
use Drupal\lesroidelareno\lesroidelareno;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * PErmet de creer une page et un menu.
 */
final class CreateUpdatePage {
  
  /**
   * Constructs a CreateUpdatePage object.
   */
  public function __construct(private readonly EntityTypeManagerInterface $entityTypeManager, private readonly StorageCacheInterface $configStorage, private readonly DuplicateEntityReference $DuplicateEntityReference) {
  }
  
  /**
   *
   * @param array $values
   * @return \Drupal\creation_site_virtuel\Entity\SiteInternetEntity
   */
  public function createUpdatePage(array $values, array $configs) {
    $uid = \Drupal::currentUser()->id();
    $mene_item = [];
    if ($configs['action_to_do'] == 'add_menu') {
      if ($configs['content_to_display'] == 'page') {
        if ($sections = $this->buildParagraphFromModel($configs['load_content'])) {
          $values['layout_paragraphs'] = $sections;
        }
      }
      elseif ($configs['content_to_display'] == 'produit') {
        $collections = array_filter($configs['configure_view']['collections'], function ($value) {
          return $value ?? false;
        });
        
        $field_products = [];
        if (!empty($configs['configure_view']['field_products'])) {
          $field_products = $configs['configure_view']['field_products'];
        }
        $paragraphValues = [
          'wbh_user_id' => $uid,
          'parent_type' => 'site_internet_entity',
          'parent_field_name' => 'layout_paragraphs'
        ];
        $Paragraph = $this->buildParagraphForProducts($paragraphValues, $field_products);
        $values['layout_paragraphs'][] = [
          'target_id' => $Paragraph->id()
        ];
        $values['hbk_collection'] = [];
        foreach ($collections as $id) {
          $values['hbk_collection'][] = [
            'target_id' => $id
          ];
        }
      }
    }
    $mene_item['title'] = $configs['menu_name'];
    $values['name'] = $configs['menu_name'];
    //
    $values['type'] = 'rc_web';
    
    $entity = SiteInternetEntity::create($values);
    $entity->save();
    // Create or update menu.
    $menu_id = lesroidelareno::getCurrentPrefixDomain(false) . '-main';
    $mene_item['menu_name'] = $menu_id;
    $mene_item['bundle'] = $menu_id;
    $mene_item['wbh_user_id'] = $uid;
    $mene_item['link'] = [
      'uri' => 'internal:/site-internet-entity/' . $entity->id(),
      'title' => null,
      'options' => []
    ];
    $mene_item['external'] = 0;
    $mene_item['expanded'] = 1;
    $mene_item['weight'] = 10;
    $this->createUpdateMenu($mene_item);
    return $entity;
  }
  
  /**
   *
   * @param array $values
   * @return \Drupal\menu_link_content\Entity\MenuLinkContent
   */
  protected function createUpdateMenu(array $values) {
    $entity = MenuLinkContent::create($values);
    $entity->save();
    return $entity;
  }
  
  protected function buildParagraphForProducts(array $values, array $field_products = []) {
    $values = $values + [
      'type' => 'produits_vetements',
      \Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD => \Drupal\lesroidelareno\lesroidelareno::getCurrentDomainId(),
      \Drupal\domain_source\DomainSourceElementManagerInterface::DOMAIN_SOURCE_FIELD => \Drupal\lesroidelareno\lesroidelareno::getCurrentDomainId()
    ];
    // On ajoute les produits principalement pour la duplication.
    if ($field_products) {
      $values['field_products'] = $field_products;
    }
    $Paragraph = Paragraph::create($values);
    $Paragraph->save();
    return $Paragraph;
  }
  
  /**
   *
   * @param string $model
   * @param array $values
   */
  protected function buildParagraphFromModel(string $model) {
    $sections = [];
    switch ($model) {
      case 'page_text':
        $id = 473;
        $sections = $this->getSectionsFromModel($id);
        break;
      
      default:
        ;
        break;
    }
    return $sections;
  }
  
  /**
   * --
   */
  protected function getSectionsFromModel(int $id) {
    if ($SiteTypeDatas = SiteTypeDatas::load($id)) {
      // Pour reduire les données qui envoye vers la duplications.
      // On initialise une nouvelle entité avec uniquement des sections.
      $values = [
        'site_internet_entity_type' => 'rc_web',
        'layout_paragraphs' => $SiteTypeDatas->get('layout_paragraphs')->getValue()
      ];
      
      $setValues = [
        \Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD => \Drupal\lesroidelareno\lesroidelareno::getCurrentDomainId(),
        \Drupal\domain_source\DomainSourceElementManagerInterface::DOMAIN_SOURCE_FIELD => \Drupal\lesroidelareno\lesroidelareno::getCurrentDomainId()
      ];
      if ($entity = $this->DuplicateEntityReference->duplicateEntity(SiteTypeDatas::create($values), false, [], $setValues, true)) {
        return $entity->get('layout_paragraphs')->getValue();
      }
    }
    return false;
  }
  
  /**
   *
   * @todo Add method description.
   */
  public function doSomething(): void {
    // @todo Place your code here.
  }
}
