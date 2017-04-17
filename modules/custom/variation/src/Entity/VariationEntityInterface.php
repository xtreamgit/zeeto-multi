<?php

namespace Drupal\variation\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Variation entities.
 *
 * @ingroup variation
 */
interface VariationEntityInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Variation entity name.
   *
   * @return string
   *   Name of the Variation entity.
   */
  public function getName();

  /**
   * Sets the Variation entity name.
   *
   * @param string $name
   *   The Variation entity name.
   *
   * @return \Drupal\variation\Entity\VariationEntityInterface
   *   The called Variation entity entity.
   */
  public function setName($name);

  /**
   * Gets the Variation entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Variation entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Variation entity creation timestamp.
   *
   * @param int $timestamp
   *   The Variation entity creation timestamp.
   *
   * @return \Drupal\variation\Entity\VariationEntityInterface
   *   The called Variation entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Variation entity published status indicator.
   *
   * Unpublished Variation entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Variation entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Variation entity.
   *
   * @param bool $published
   *   TRUE to set this Variation entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\variation\Entity\VariationEntityInterface
   *   The called Variation entity entity.
   */
  public function setPublished($published);

  /**
   * Gets the Variation entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Variation entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\variation\Entity\VariationEntityInterface
   *   The called Variation entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Variation entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Variation entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\variation\Entity\VariationEntityInterface
   *   The called Variation entity entity.
   */
  public function setRevisionUserId($uid);

}
