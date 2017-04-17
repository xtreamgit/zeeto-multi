<?php

namespace Drupal\variation;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Variation entity entity.
 *
 * @see \Drupal\variation\Entity\VariationEntity.
 */
class VariationEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\variation\Entity\VariationEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished variation entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published variation entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit variation entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete variation entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add variation entities');
  }
}
