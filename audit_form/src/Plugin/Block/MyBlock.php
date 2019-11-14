<?php
/**
 * @file
 * Contains \Drupal\audit_form\Plugin\Block\MYBlock.
 */
namespace Drupal\audit_form\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
/**
 * Provides a 'article' block.
 *
 * @Block(
 *   id = "my_block",
 *   admin_label = @Translation("MY block"),
 *   category = @Translation("My Custom article block example")
 * )
 */
class MyBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\audit_form\Form\VirtualForm');
    return $form;
   }
}
