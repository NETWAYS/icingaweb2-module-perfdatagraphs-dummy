<?php

namespace Icinga\Module\Perfdatagraphsdummy\Forms;

use Icinga\Forms\ConfigForm;

/**
 * PerfdataGraphsDummyConfigForm represents the configuration form for the PerfdataGraphs Dummy Module.
 */
class PerfdataGraphsDummyConfigForm extends ConfigForm
{
    public function init()
    {
        $this->setName('form_config_resource');
        $this->setSubmitLabel($this->translate('Save Changes'));
        $this->setValidatePartial(true);
    }

    public function createElements(array $formData)
    {
        $this->addElement('text', 'dummy_config', [
            'description' => t('Do you really need help for this one?'),
            'label' => 'What is 1 + 1?'
        ]);

        $this->addElement('checkbox', 'dummy_error_mode', [
            'description' => t('Throw errors instead of data'),
            'label' => 'Throw errors'
        ]);
    }

    public function addSubmitButton()
    {
        parent::addSubmitButton()
            ->getElement('btn_submit')
            ->setDecorators(['ViewHelper']);

        $this->addElement(
            'submit',
            'resource_validation',
            [
                'ignore' => true,
                'label' => $this->translate('Validate Configuration'),
                'data-progress-label' => $this->translate('Validation In Progress'),
                'decorators' => ['ViewHelper']
            ]
        );

        $this->setAttrib('data-progress-element', 'resource-progress');
        $this->addElement(
            'note',
            'resource-progress',
            [
                'decorators' => [
                    'ViewHelper',
                    ['Spinner', ['id' => 'resource-progress']]
                ]
            ]
        );

        $this->addDisplayGroup(
            ['btn_submit', 'resource_validation', 'resource-progress'],
            'submit_validation',
            [
                'decorators' => [
                    'FormElements',
                    ['HtmlTag', ['tag' => 'div', 'class' => 'control-group form-controls']]
                ]
            ]
        );

        return $this;
    }

    public static function validateFormData($form)
    {
        if ($form->getValue('dummy_config') === "2") {
            return ['output' => ['Nice!']];
        }

        return ['error' => 'That is not correct', 'output' => ['Try again dummy!']];
    }

    public function isValidPartial(array $formData)
    {
        if ($this->getElement('resource_validation')->isChecked() && parent::isValid($formData)) {
            $validation = static::validateFormData($this);
            if ($validation !== null) {
                $this->addElement(
                    'note',
                    'inspection_output',
                    [
                        'order' => 0,
                        'value' => '<strong>' . $this->translate('Validation Log') . "</strong>\n\n"
                            . join("\n", $validation['output'] ?? []),
                        'decorators' => [
                            'ViewHelper',
                            ['HtmlTag', ['tag' => 'pre', 'class' => 'log-output']],
                        ]
                    ]
                );

                if (isset($validation['error'])) {
                    $this->warning(sprintf(
                        $this->translate('Failed to successfully validate the configuration: %s'),
                        $validation['error']
                    ));
                    return false;
                }
            }

            $this->info($this->translate('The configuration has been successfully validated.'));
        }

        return true;
    }
}
