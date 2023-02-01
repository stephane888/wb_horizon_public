<?php

namespace Drupal\wb_horizon_public\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Annotation\FieldWidget;
use Drupal\Core\Annotation\Translation;

/**
 * Plugin implementation of the 'creneau_widget_type' widget.
 *
 * @FieldWidget(
 *   id = "creneau_widget_type",
 *   module = "wb_horizon_public",
 *   label = @Translation("Creneau widget type"),
 *   field_types = {
 *     "creneau_field_type"
 *   }
 * )
 */
class CreneauWidgetType extends WidgetBase {
  
  /**
   *
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'size' => 60,
      'placeholder' => '',
      'interval' => 120,
      'decalage_creneau' => 120,
      'heure_debut' => '7h00',
      'heure_fin' => '20h30'
    ] + parent::defaultSettings();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];
    $elements['interval'] = [
      '#type' => 'number',
      '#title' => t('interval'),
      '#default_value' => $this->getSetting('interval'),
      '#required' => TRUE,
      '#min' => 10,
      '#step' => 10
    ];
    $elements['decalage_creneau'] = [
      '#type' => 'number',
      '#title' => t(' Decalage creneau '),
      '#default_value' => $this->getSetting('decalage_creneau'),
      '#required' => TRUE,
      '#min' => 10,
      '#step' => 10
    ];
    return $elements;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = t('interval: @interval minutes', [
      '@interval' => $this->getSetting('interval')
    ]);
    $summary[] = t('decalage_creneau: @decalage_creneau minutes', [
      '@decalage_creneau' => $this->getSetting('decalage_creneau')
    ]);
    return $summary;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $heure_debut = explode("h", $this->getSetting('heure_debut'));
    $dateDebut = new \DateTime();
    $dateDebut->setTime($heure_debut[0], $heure_debut[1]);
    //
    $heure_fin = explode("h", $this->getSetting('heure_fin'));
    $dateFin = new \DateTime();
    $dateFin->setTime($heure_fin[0], $heure_fin[1]);
    //
    $interval = $this->getSetting('interval');
    $decalage_creneau = $this->getSetting('decalage_creneau');
    //
    $options = [];
    $k = 0;
    while (($dateFin >= $dateDebut) && $k < 50) {
      $cr = $dateDebut->format("H\hi");
      $options[$cr] = $cr;
      $dateDebut->modify('+' . $interval . ' minutes');
      $k++;
    }
    // dump($options);
    //
    $element['value'] = $element + [
      '#type' => 'radios',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
      '#options' => $options
    ];
    return $element;
  }
  
  /**
   * --
   *
   * {@inheritdoc}
   * @see \Drupal\Core\Field\WidgetBase::massageFormValues()
   */
  public function massageFormValues($values, $form, $form_state) {
    $values = parent::massageFormValues($values, $form, $form_state);
    foreach ($values as &$value) {
      if (!isset($value['end_value'])) {
        $value['end_value'] = '';
      }
    }
    return $values;
  }
  
}
