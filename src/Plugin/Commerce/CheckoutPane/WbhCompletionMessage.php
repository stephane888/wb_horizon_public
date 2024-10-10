<?php

namespace Drupal\wb_horizon_public\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CompletionMessage as DefaultCompletionMessage;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the completion message pane.
 *
 * @CommerceCheckoutPane(
 *   id = "wbu_completion_message",
 *   label = @Translation("WBU Completion message"),
 *   default_step = "complete",
 * )
 */
class WbhCompletionMessage extends DefaultCompletionMessage {
  
  /**
   *
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'message' => [
        'value' => "You can view your order on your account page when logged in.",
        'format' => 'plain_text'
      ],
      'title' => "Your order number is [commerce_order:order_number]."
    ] + parent::defaultConfiguration();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#required' => true,
      '#attributes' => [
        'class' => [
          'username'
        ]
      ],
      '#default_value' => $this->configuration['title']
    ];
    $form['message'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Message'),
      '#description' => $this->t('Shown the end of checkout, after the customer has placed their order.'),
      '#default_value' => $this->configuration['message']['value'],
      '#format' => $this->configuration['message']['format'],
      '#element_validate' => [
        'token_element_validate'
      ],
      '#token_types' => [
        'commerce_order'
      ],
      '#required' => TRUE
    ];
    $form['token_help'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => [
        'commerce_order'
      ]
    ];
    
    return $form;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['title'] = $values['title'];
      $this->configuration['message'] = $values['message'];
    }
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $title = $this->token->replace(t($this->configuration['title']), [
      'commerce_order' => $this->order
    ]);
    $pane_form['message'] = [
      '#theme' => 'commerce_checkout_completion_message',
      '#order_entity' => $this->order,
      '#message' => [
        [
          '#tag' => 'h3',
          '#type' => 'html_tag',
          '#attributes' => [],
          '#value' => t($title)
        ],
        [
          '#type' => 'processed_text',
          '#text' => t($this->configuration['message']['value']),
          '#format' => $this->configuration['message']['format']
        ],
        [
          '#tag' => 'small',
          '#type' => 'html_tag',
          '#attributes' => [],
          '#value' => t('(Please also check your spam folder. )')
        ]
      ]
    ];
    
    return $pane_form;
  }
}