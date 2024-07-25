<?php

namespace Drupal\wb_horizon_public\Controller;

use Symfony\Component\HttpFoundation\Request;
use Drupal\booking_system\Controller\BookingSystemUseApp;
use Drupal\booking_system\Services\BookingManager\ManagerCreneaux;
use Drupal\booking_system\Services\BookingManager\ManagerDate;
use Drupal\wb_horizon_public\Services\SourceManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Ce controlleur permet de fournir les routes pour l'application de
 * resrvation de creneau ( application de base ).
 *
 * @author stephane
 *        
 */
class BookingSystemUseAppPublic extends BookingSystemUseApp {
  /**
   * @var SourceManager
   */
  protected $sourceManager;

  public function __construct(ManagerDate $ManagerDate, ManagerCreneaux $ManagerCreneaux, SourceManager $source_manager) {
    $this->BookingMangerDate = $ManagerDate;
    $this->ManagerCreneaux = $ManagerCreneaux;
    $this->sourceManager = $source_manager;
  }

  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('booking_system.app_manager_date'),
      $container->get('booking_system.app_manager_creneaux'),
      $container->get('wb_horizon_public.source_manager')
    );
  }


  /**
   * Permet de charger la configuration par defaut.
   */
  public function loadConfigCalandar(Request $Request) {
    $entity_type_id = "booking_config_type";
    $entityConfig = $this->sourceManager->getEntityConfig();
    return $this->Views($Request, $entityConfig->id());
  }

  /**
   * Permet de recuperer les données de configurations pour la construction des
   * creneaux.
   *
   * @param string $booking_config_type_id
   * @param string $date
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function loadConfisCreneaux($booking_config_type_id, $date) {
    return parent::loadConfisCreneaux($booking_config_type_id, $date);
  }

  /**
   * Permet de generer et de configurer RDV par domaine.
   */
  public function ConfigureDefault() {
    $entity_type_id = "booking_config_type";
    $entityConfig = $this->sourceManager->getEntityConfig();

    $form = $this->entityFormBuilder()->getForm($entityConfig, "edit", [
      'redirect_route' => 'bookingsystem_autoecole.config_resume',
      'booking_config_type_id' => $entityConfig->id()
    ]);
    return $form;
  }

  /**
   * Enregistrer un creneau.
   *
   * @param string $booking_config_type_id
   */
  public function SaveReservation(Request $Request, string $booking_config_type_id) {
    return parent::SaveReservation($Request, $booking_config_type_id);
  }
}
