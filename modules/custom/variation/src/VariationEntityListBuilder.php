<?php

namespace Drupal\variation;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Variation entities.
 *
 * @ingroup variation
 */
class VariationEntityListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Variation ID');
    $header['first_url'] = $this->t('First URL');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    global $base_url;
    /* @var $entity \Drupal\variation\Entity\VariationEntity */
    $row['id'] = $entity->variation_id->value;
    $row['first_url'] = $base_url . '/' . $entity->variation_id->value . '/pg1';
    return $row + parent::buildRow($entity);
  }
}
