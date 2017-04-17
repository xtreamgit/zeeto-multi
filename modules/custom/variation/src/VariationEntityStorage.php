<?php

namespace Drupal\variation;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\variation\Entity\VariationEntityInterface;

/**
 * Defines the storage handler class for Variation entities.
 *
 * This extends the base storage class, adding required special handling for
 * Variation entities.
 *
 * @ingroup variation
 */
class VariationEntityStorage extends SqlContentEntityStorage implements VariationEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(VariationEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {variation_entity_revision} WHERE id=:id ORDER BY vid',
      array(':id' => $entity->id())
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {variation_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      array(':uid' => $account->id())
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(VariationEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {variation_entity_field_revision} WHERE id = :id AND default_langcode = 1', array(':id' => $entity->id()))
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('variation_entity_revision')
      ->fields(array('langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED))
      ->condition('langcode', $language->getId())
      ->execute();
  }
}
