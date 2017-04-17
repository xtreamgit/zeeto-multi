<?php

namespace Drupal\variation\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\variation\Entity\VariationEntityInterface;

/**
 * Class VariationEntityController.
 *
 *  Returns responses for Variation entity routes.
 *
 * @package Drupal\variation\Controller
 */
class VariationEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Variation entity  revision.
   *
   * @param int $variation_entity_revision
   *   The Variation entity  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($variation_entity_revision) {
    $variation_entity = $this->entityManager()->getStorage('variation_entity')->loadRevision($variation_entity_revision);
    $view_builder = $this->entityManager()->getViewBuilder('variation_entity');

    return $view_builder->view($variation_entity);
  }

  /**
   * Page title callback for a Variation entity  revision.
   *
   * @param int $variation_entity_revision
   *   The Variation entity  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($variation_entity_revision) {
    $variation_entity = $this->entityManager()->getStorage('variation_entity')->loadRevision($variation_entity_revision);
    return $this->t('Revision of %title from %date', array('%title' => $variation_entity->label(), '%date' => format_date($variation_entity->getRevisionCreationTime())));
  }

  /**
   * Generates an overview table of older revisions of a Variation entity .
   *
   * @param \Drupal\variation\Entity\VariationEntityInterface $variation_entity
   *   A Variation entity  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(VariationEntityInterface $variation_entity) {
    $account = $this->currentUser();
    $langcode = $variation_entity->language()->getId();
    $langname = $variation_entity->language()->getName();
    $languages = $variation_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $variation_entity_storage = $this->entityManager()->getStorage('variation_entity');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $variation_entity->label()]) : $this->t('Revisions for %title', ['%title' => $variation_entity->label()]);
    $header = array($this->t('Revision'), $this->t('Operations'));

    $revert_permission = (($account->hasPermission("revert all variation entity revisions") || $account->hasPermission('administer variation entities')));
    $delete_permission = (($account->hasPermission("delete all variation entity revisions") || $account->hasPermission('administer variation entities')));

    $rows = array();

    $vids = $variation_entity_storage->revisionIds($variation_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\variation\VariationEntityInterface $revision */
      $revision = $variation_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->revision_timestamp->value, 'short');
        if ($vid != $variation_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.variation_entity.revision', ['variation_entity' => $variation_entity->id(), 'variation_entity_revision' => $vid]));
        }
        else {
          $link = $variation_entity->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->revision_log_message->value, '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.variation_entity.translation_revert', ['variation_entity' => $variation_entity->id(), 'variation_entity_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.variation_entity.revision_revert', ['variation_entity' => $variation_entity->id(), 'variation_entity_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.variation_entity.revision_delete', ['variation_entity' => $variation_entity->id(), 'variation_entity_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['variation_entity_revisions_table'] = array(
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    );

    return $build;
  }

}
