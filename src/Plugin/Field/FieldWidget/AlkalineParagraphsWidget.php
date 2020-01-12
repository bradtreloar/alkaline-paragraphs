<?php

namespace Drupal\alkaline_paragraphs\Plugin\Field\FieldWidget;

use Drupal\paragraphs\Plugin\Field\FieldWidget\ParagraphsWidget;

/**
 * Plugin implementation of the 'entity_reference_revisions paragraphs' widget.
 *
 * @FieldWidget(
 *   id = "paragraphs_alkaline",
 *   label = @Translation("Paragraphs Alkaline"),
 *   description = @Translation("Alkaline pagebuilder widget."),
 *   field_types = {
 *     "entity_reference_revisions"
 *   }
 * )
 */
class AlkalineParagraphsWidget extends ParagraphsWidget {

  /**
   * @inheritdoc
   */
  protected function buildModalAddForm(array &$element) {
    parent::buildModalAddForm($element);
    // Attach extra library to provide CSS for the modified widget.
    $element['#attached']['library'][] = 'alkaline_paragraphs/drupal.alkaline_paragraphs.modal';
  }

  /**
   * @inheritdoc
   */
  protected function buildButtonsAddMode() {
    // We only want to override the function's behaviour if the widget is
    // using the modal add mode.
    if ($this->getSetting('add_mode') != 'modal') {
      return parent::buildButtonsAddMode();
    }

    $options = $this->getAccessibleOptions();
    $add_mode = $this->getSetting('add_mode');
    $paragraphs_type_storage = \Drupal::entityTypeManager()->getStorage('paragraphs_type');

    $add_more_elements = [];

    foreach ($options as $machine_name => $label) {
      $button_key = 'add_more_button_' . $machine_name;
      /** @var \Drupal\paragraphs\Entity\ParagraphsType $paragraph_type */
      $paragraph_type = $paragraphs_type_storage->load($machine_name);

      $button = $this->expandButton([
        '#type' => 'submit',
        '#name' => $this->fieldIdPrefix . '_' . $machine_name . '_add_more',
        '#attributes' => ['class' => ['field-add-more-submit', 'paragraphs-add-wrapper']],
        '#limit_validation_errors' => [array_merge($this->fieldParents, [$this->fieldDefinition->getName(), 'add_more'])],
        '#submit' => [[get_class($this), 'addMoreSubmit']],
        '#ajax' => [
          'callback' => [get_class($this), 'addMoreAjax'],
          'wrapper' => $this->fieldWrapperId,
        ],
        '#bundle_machine_name' => $machine_name,
      ]);

      $button['label'] = [
        [
          '#prefix' => '<div class="button-label">',
          '#markup' => $label,
          '#suffix' => '</div>',
        ],
        [
          '#prefix' => '<div class="button-description">',
          '#markup' => $paragraph_type->getDescription(),
          '#suffix' => '</div>',
        ],
      ];

      if ($icon_url = $paragraph_type->getIconUrl()) {
        $button['#attributes']['style'] = "background-image: url('$icon_url')";
      }

      $add_more_elements[$button_key] = $button;
    }

    $this->buildModalAddForm($add_more_elements);
    $add_more_elements['add_modal_form_area']['#suffix'] = $this->t('to %type', ['%type' => $this->fieldDefinition->getLabel()]);
    $add_more_elements['#weight'] = 1;

    return $add_more_elements;
  }
}
