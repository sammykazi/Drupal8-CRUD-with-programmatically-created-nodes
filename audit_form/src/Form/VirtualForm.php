<?php

/**
 * @file
 * Contains \Drupal\audit_form\Form\VirtualForm.
 */

namespace Drupal\audit_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * targeted form.
 */

class VirtualForm extends FormBase {
   /**
   * {@inheritdoc}
   */
  public function getFormId() {

        return 'audit_form_virtual_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $conn = Database::getConnection();
     $record = array();
    if (isset($_GET['sno'])) {
        $query = $conn->select('stark', 's')
            ->condition('sno', $_GET['sno'])
            ->fields('s');
        $record = $query->execute()->fetchAssoc();
    }

        $form['sno'] = array(
        '#type' => 'textfield',
        '#title' => t('SrNo'),
        '#required' => TRUE,
         '#default_value' => (isset($record['sno']) && $_GET['sno']) ? $record['sno']:'',

        );
        $form['title'] = array(
          '#type' => 'textfield',
          '#title' => t('Title'),
          '#required' => TRUE,

           '#default_value' => (isset($record['title']) && $_GET['sno']) ? $record['title']:'',

        );

        $form['datetime'] = array(
            '#type'=> 'datetime',
            '#title'=> 'datetime',
            // '#options' => $this->getDate(),
            //'#date_time_step'=> '60',
            // '#default_value' =>
            '#default_value' => (isset($record['datetime']) && $_GET['sno'])? DrupalDateTime::createFromTimestamp(strtotime($record['datetime'])):'',



        );

        $form['description'] = array(
            '#type' => 'textarea',
            '#title' => t('Description'),
            '#required' => TRUE,
             '#default_value' => (isset($record['description']) && $_GET['sno']) ? $record['description']:'',

        );
        $form['image'] = array(
                '#type' => 'managed_file',
                '#upload_location' => 'public://upload/hello',
                '#title' => t('Image'),
                '#description' => t('The image to display'),
                '#required' => true,
                 '#default_value' => (isset($record['image']) && $_GET['sno']) ? [$record['image']]:'',

        );

      $form['dropdown'] = array(
        '#type' => 'select',
        '#required' => FALSE,
        '#title' => t('Dropdown'),
        '#options' => $this->getAction(),
         '#default_value' => (isset($record['dropdown']) && $_GET['sno']) ? $record['dropdown']:'',

    );

        $query = \Drupal::database()->select('stark', 's');
        $query->fields('s', ['sno', 'title','datetime','description','image', 'dropdown']);
        $pager = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(5);
        $results = $pager->execute()->fetchAll();

        $header = [
          'sno' => t('sno'),
          'title' => t('title'),
          'datetime' => t('datetime'),
          'description' => t('description'),
          'image' => t('image'),
          'dropdown' => t('dropdown'),
          'operations' => t('operations'),
          'operations1' => t('operations1'),
        ];

        // Initialize an empty array
          $output = array();

        // Next, loop through the $results array
           //echo"<pre>";print_r($results);exit;
          foreach ($results as $result) {
            if ($result->sno != 0 && $result->sno != 1) {
              $edit = Url::fromUserInput('/audit-form?sno='.$result->sno);
              $delete = Url::fromUserInput('/audit-form/form/delete/'.$result->sno);
          $output[$result->sno] = [
            'sno' => $result->sno,
            'title' => $result->title,
            'datetime' => $result->datetime,
            'description' => $result->description,

            'image' =>$result->image,
            'dropdown' =>$result->dropdown,

            'operations' => \Drupal::l('Edit',$edit),
            'operations1' => \Drupal::l('Delete',$delete),
            ];
          }
        }
           $form['submit'] = array(
          '#type' => 'submit',
          '#value' => t('Submit'),
        );


          $form['table'] = [
          '#type' => 'tableselect',
          '#header' => $header,
          '#options' => $output,
          //'#collapsible' => TRUE,
          //'#collapsed' => FALSE,
          //'#tree' => FALSE,
          //'#weight' => -2,
          '#empty' => t('No users found'),
          ];


        $form['pager'] = array(
          '#type' => 'pager'
    );

        return $form;
      }

  /**
    *{@inheritdoc}
  */

  public function getAction() {
    return [
      '' => 'Select Options ',
      'Active' => 'Active',
      'InActive' => 'InActive',
    ];
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

    $field=$form_state->getValues();
//print_r($form_state->getValue('Datetime'));exit;
    $my_news = Node::create(['type' => 'news']);
    $my_news->set('title', $form_state->getValue('title'));
    $my_news->set('body', $form_state->getValue('description'));
    $my_news->set('field_datetime', $form_state->getValue('datetime'));
    $my_news->set('field_usertype', $form_state->getValue('dropdown'));
    $my_news->set('field_image1', $form_state->getValue('image'));
    $my_news->enforceIsNew();
    $my_news->save();

    $sno=$field['sno'];
    $title=$field['title'];
    $datetime=$field['datetime'];
    //$image_fid = NULL;
    $description=$field['description'];
    $dropdown=$field['dropdown'];
    $image=$field['image'];

    /* if (!empty($imag)) {
      $fid = $imag[0];
    }
    $image = $field['fid'];*/

    //echo"<pre>";print_r( $field);exit;

    if (isset($_GET['sno'])) {

      $field = array(
      'sno' => $sno,
      'title' => $title,
      'datetime' =>$datetime->format("Y-m-d H:i:s"),
      'description' => $description,

      'image' => $image[0],

      'dropdown' => $dropdown,

      );

      //$drupal_file_uri = File::load($fid)->getFileUri();
     // $image = file_url_transform_relative(file_create_url($drupal_file_uri));
      //echo"<pre>";print_r( $field);exit;
      $query = \Drupal::database();
      $query->update('stark')
            ->fields($field)
            ->condition('sno', $_GET['sno'])
            ->execute();
      drupal_set_message('Succesfully updated');
      $form_state->setRedirect('audit_form_list');
    }
    else
    {
    $sno = $form_state->getValue('sno');
//echo $sno;exit;
    $title = $form_state->getValue('title');

    $datetime = $form_state->getValue('datetime')->format("Y-m-d H:i:s");
	// echo"<pre>";print_r($datetime->format("Y-m-d H:i:s"));exit;

    $description = $form_state->getValue('description');

    $image = $form_state->getValue('image');

    $dropdown = $form_state->getValue('dropdown');

    \Drupal::database()->insert('stark')
    ->fields([
      'sno',
      'title',
      'datetime',
      'description',
      'image',
      'dropdown',

    ])
    ->values(array(
      $sno,
      $title,
      $datetime,
      $description,
      $image[0],
      $dropdown

    ))
    ->execute();
    }
    drupal_set_message("Record inserted successfully");
  }
}


?>
