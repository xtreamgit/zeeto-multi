<?php

namespace Drupal\variation\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Variation entity entities.
 */
class VariationEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
