<?php

namespace Drupal\audit_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Render\Element;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;


/**
 * Class DeleteForm.
 *
 * @package Drupal\auditform\Form
 */
class DeleteForm extends ConfirmFormBase {

/**
 * {@inheritdoc}
 */

 public $cid;

    public function getFormId() {
        return 'delete_form';
    }

    public function getQuestion() {
        return t('Are you sure you want to delete  the record %cid?', array ('%cid' => $this->cid));
    }

    public function getConfirmText() {
        return t('Delete it!');
    }


    public function getCancelUrl() {
        return new Url('audit_form_list');
    }

/**
 * {@inheritdoc}
 */

    public function buildForm(array $form, FormStateInterface $form_state, $cid = NULL) {
        $this->sno = $cid;
        return parent::buildForm($form, $form_state);
    }

/**
 * {@inheritdoc}
 */

    public function validateForm(array &$form, FormStateInterface $form_state) {

    }

/**
 * {@inheritdoc}
 */

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $query = \Drupal::database();
        $query->delete('stark')
              ->condition('sno', $this->sno)
              ->execute();
        drupal_set_message("succesfully deleted");
        $form_state->setRedirect('audit_form_list');
        }
/*
 Delete the record from our table.
\Drupal::database()->delete('TABLE_NAME')
	->condition(CONDITION) // Condition for Remove specific records.
	->execute();

<u><strong>Example:</strong></u>
$query = \Drupal::database()->delete('employee', 'emp');
	->condition('employee_id', 'CE 003')
	->execute();
*/
}
?>
