<?php

namespace branchonline\joyride;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Description of Joyride
 *
 * @author jap
 */
class Joyride extends InputWidget {

    const POS_BOTTOM = 'bottom';
    const POS_TOP = 'top';    
    
    /**
     * @var array list of joyride items
     */
    public $items = [];

    /**
     * @var array list of HTML attributes for the <li> Joyride tag.
     */
    public $item_options = [];
    
    /**
     * These options will be passed to the Joyride JS call.
     * 
     * @var array joyride options
     */
    public $joyride_options = [];

    public function init() {
        if (is_null($this->name)) {
            throw new InvalidConfigException('Name property must be specified.');
        }

        parent::init();
        $view = $this->getView();
        JoyrideAsset::register($view);
        
        
        $joyride_options_strings = [];
        $this->joyride_options['autoStart'] = 'true';
        foreach($this->joyride_options as $k => $v) {
            $joyride_options_strings[] = $k . ': "' . $v . '"';
        }
        
        $view->registerJs('console.log($("#' . $this->getId() . '")); $("#' . $this->getId() . '").joyride({
             '.  implode(', ', $joyride_options_strings).'
        });');
    }

    public function run() {
        Yii::$app->view->on(View::EVENT_END_BODY, function() {
            echo Html::tag('ol', $this->renderElements(), [
                'id' => $this->getId(),
            ]);
        });
    }

    protected function renderElements() {
        if (empty($this->items)) {
            throw new InvalidConfigException("The [items] option is required.");
        }

        $required = ['target_id', 'header', 'content'];

        $collection = [];
        foreach ($this->items as $i => $item) {
            if (count(array_intersect_key(array_flip($required), $item)) !== count($required)) {
                throw new InvalidConfigException("Mandatory key missing..");
            }

            $options = array_merge(
                    $this->item_options, ArrayHelper::getValue($item, 'options', []), ['data-id' => $item['target_id']]
            );

            $content = Html::tag('h4', $item['header']) . $item['content'];
            $collection[] = Html::tag('li', $content, $options);
        }
        return implode('', $collection);
    }

}
