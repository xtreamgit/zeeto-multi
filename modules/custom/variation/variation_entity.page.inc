<?php

/**
 * @file
 * Contains variation_entity.page.inc.
 *
 * Page callback for Variation entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Variation entity templates.
 *
 * Default template: variation_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_variation_entity(array &$variables) {
  // Fetch VariationEntity Entity Object.
  $variation_entity = $variables['elements']['#variation_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
