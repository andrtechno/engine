<?php

namespace panix\engine\behaviors\wizard;

class WizardEvent extends \yii\base\Event {

    const WIZARD_START = 'wizardStart';
    const WIZARD_FINISHED = 'wizardFinished';
    const WIZARD_PROCESS_STEP = 'wizardProcessStep';
    const WIZARD_INVALID_STEP = 'wizardInvalidStep';
    const WIZARD_RESET = 'wizardReset';
    const WIZARD_CANCEL = 'wizardCancel';
    const WIZARD_EXPIRED = 'wizardExpired';
    const WIZARD_SAVE_DRAFT = 'wizardSaveDraft';

    public $wizardData = [];
    public $step;

    /**
     * WizardEvent factory
     *
     * @param \yii\base\Object $sender
     * @param string|null $step
     * @param array $wizardData
     * @return WizardEvent
     */
    public static function create($sender, $step = null, $wizardData = null) {
        return new static([
            'sender' => $sender,
            'step' => $step,
            'wizardData' => $wizardData
        ]);
    }

}
