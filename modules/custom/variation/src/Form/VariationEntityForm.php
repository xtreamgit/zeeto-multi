<?php

namespace Drupal\variation\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Variation entity edit forms.
 *
 * @ingroup variation
 */
class VariationEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\variation\Entity\VariationEntity */
    $form = parent::buildForm($form, $form_state);

    // Disable user editing of variation ID.
    $form['variation_id']['#disabled'] = TRUE;

    if (!$this->entity->isNew()) {
      $form['new_revision'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Create new revision'),
        '#default_value' => FALSE,
        '#weight' => 10,
      );
    }

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    // Get pages and remove final page.
    $pages = variation_get_pages($entity);
    array_pop($pages);

    $this->recreateVariantAliases($pages, $entity->variation_id->value);

    // Set the Variation ID on initial save.
    if ($entity->isNew()) {
      $entity->variation_id->value = $this->generateVariationId();
    }

    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('new_revision') && $form_state->getValue('new_revision') != FALSE) {
      $entity->setNewRevision();

      // If a new revision is created, save the current user as revision author.
      $entity->setRevisionCreationTime(REQUEST_TIME);
      $entity->setRevisionUserId(\Drupal::currentUser()->id());
    }
    else {
      $entity->setNewRevision(FALSE);
    }

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Variation entity.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Variation entity.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.variation_entity.canonical', ['variation_entity' => $entity->id()]);
  }

  /**
   * Generate a unique 6 digit variation ID.
   *
   * @return string
   *   A unique 6 digit variation ID.
   */
  private function generateVariationId() {
    return variation_generate_id();
  }

  private function recreateVariantAliases($pages, $vid) {
    variation_recreate_variant_aliases($pages, $vid);
  }
}
