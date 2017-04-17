<?php

namespace Drupal\variation;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface VariationEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Variation entity revision IDs for a specific Variation entity.
   *
   * @param \Drupal\variation\Entity\VariationEntityInterface $entity
   *   The Variation entity entity.
   *
   * @return int[]
   *   Variation entity revision IDs (in ascending order).
   */
  public function revisionIds(VariationEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Variation entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Variation entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\variation\Entity\VariationEntityInterface $entity
   *   The Variation entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(VariationEntityInterface $entity);

  /**
   * Unsets the language for all Variation entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);
}
