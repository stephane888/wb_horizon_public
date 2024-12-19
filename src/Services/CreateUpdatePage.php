<?php
declare(strict_types = 1);

namespace Drupal\wb_horizon_public\Services;

use Drupal\Core\Config\StorageCacheInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\creation_site_virtuel\Entity\SiteInternetEntity;
use Drupal\creation_site_virtuel\Entity\SiteTypeDatas;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\apivuejs\Services\DuplicateEntityReference;

/**
 *
 * @todo Add class description.
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
    dd($configs);
    $entity = SiteInternetEntity::create($values);
    // $entity->save();
    return $entity;
  }
  
  /**
   *
   * @param array $values
   * @return \Drupal\menu_link_content\Entity\MenuLinkContent
   */
  public function createUpdateMenu(array $values) {
    $entity = MenuLinkContent::create($values);
    $entity->save();
    return $entity;
  }
  
  /**
   *
   * @param string $model
   * @param array $values
   */
  public function buildParagraphFromModel(string $model, array &$values) {
    $sections = [];
    switch ($model) {
      case 'page_text':
        $id = '473';
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
        'layout_paragraphs' => $SiteTypeDatas->get('layout_paragraphs')->getValue()
      ];
      if ($entity = $this->DuplicateEntityReference->duplicateEntity(SiteTypeDatas::create($values), false)) {
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
