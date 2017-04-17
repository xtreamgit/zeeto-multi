<?php

namespace Drupal\variation\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Variation entity.
 *
 * @ingroup variation
 *
 * @ContentEntityType(
 *   id = "variation_entity",
 *   label = @Translation("Variation entity"),
 *   handlers = {
 *     "storage" = "Drupal\variation\VariationEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\variation\VariationEntityListBuilder",
 *     "views_data" = "Drupal\variation\Entity\VariationEntityViewsData",
 *     "translation" = "Drupal\variation\VariationEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\variation\Form\VariationEntityForm",
 *       "add" = "Drupal\variation\Form\VariationEntityForm",
 *       "edit" = "Drupal\variation\Form\VariationEntityForm",
 *       "delete" = "Drupal\variation\Form\VariationEntityDeleteForm",
 *     },
 *     "access" = "Drupal\variation\VariationEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\variation\VariationEntityHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "variation_entity",
 *   data_table = "variation_entity_field_data",
 *   revision_table = "variation_entity_revision",
 *   revision_data_table = "variation_entity_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer variation entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/variation/{variation_entity}",
 *     "add-form" = "/admin/structure/variation/add",
 *     "edit-form" = "/admin/structure/variation/{variation_entity}/edit",
 *     "delete-form" = "/admin/structure/variation/{variation_entity}/delete",
 *     "version-history" = "/admin/structure/variation/{variation_entity}/revisions",
 *     "revision" = "/admin/structure/variation/{variation_entity}/revisions/{variation_entity_revision}/view",
 *     "revision_revert" = "/admin/structure/variation/{variation_entity}/revisions/{variation_entity_revision}/revert",
 *     "translation_revert" = "/admin/structure/variation/{variation_entity}/revisions/{variation_entity_revision}/revert/{langcode}",
 *     "revision_delete" = "/admin/structure/variation/{variation_entity}/revisions/{variation_entity_revision}/delete",
 *     "collection" = "/admin/structure/variation",
 *   },
 *   field_ui_base_route = "variation_entity.settings"
 * )
 */
class VariationEntity extends RevisionableContentEntityBase implements VariationEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the variation_entity owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFinalPage() {
    return $this->get('final_page')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setFinalPage($final_page) {
    $this->set('final_page', $final_page);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getVariant() {
    return $this->get('variant')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setVariant($variant) {
    $this->set('variant', $variant);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getVariantId() {
    return $this->get('variant_id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setVariantId($variant_id) {
    $this->set('variant_id', $variant_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionCreationTime() {
    return $this->get('revision_timestamp')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionCreationTime($timestamp) {
    $this->set('revision_timestamp', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionUser() {
    return $this->get('revision_uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionUserId($uid) {
    $this->set('revision_uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['variation_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Variation ID'))
      ->setDescription(t('The variation ID.'))
      ->setRevisionable(TRUE)
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the variation.'))
      ->setRevisionable(TRUE)
      ->setRequired(TRUE)
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => 1,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => 1,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['variant'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Theme'))
      ->setDescription(t('Theme to apply to the variation\'s nodes.'))
      ->setRevisionable(TRUE)
      ->setRequired(TRUE)
      ->setSettings(array(
        'allowed_values' => \Drupal::service('entity_display.repository')->getViewModeOptions('node'),
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'list_default',
        'weight' => 2,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => 2,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['pages'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Pages'))
      ->setDescription(t('The pages in the variant.'))
      ->setSettings(array(
        'target_type' => 'node',
        'default_value' => 0,
      ))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => 3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 3,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['final_page'] = BaseFieldDefinition::create('uri')
      ->setLabel(t('Final Page'))
      ->setDescription(t('The final page to send the user to.'))
      ->setRevisionable(TRUE)
      ->setRequired(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'uri_link',
        'weight' => 4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'uri',
        'weight' => 4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the variation.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Variation entity is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_timestamp'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Revision timestamp'))
      ->setDescription(t('The time that the current revision was created.'))
      ->setQueryable(FALSE)
      ->setRevisionable(TRUE);

    $fields['revision_uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Revision user ID'))
      ->setDescription(t('The user ID of the author of the current revision.'))
      ->setSetting('target_type', 'user')
      ->setQueryable(FALSE)
      ->setRevisionable(TRUE);

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }
}
