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

        if(isset($this->joyride_options['cookieMonster']) && $this->joyride_options['cookieMonster'] === true) {
            $this->publishAndRegisterFile('@vendor/branchonline/yii2-joyride/src/assets/jquery.cookie.js', ['depends' => JoyrideAsset::className()]);
        }
        
        $joyride_options_strings = [];
        $this->joyride_options['autoStart'] = 'true';
        foreach($this->joyride_options as $k => $v) {
            if(is_bool($v)) {
                $value = $v ? 'true' : 'false';
            } else {
                $value = "'" . $v . "'";
            }
            $joyride_options_strings[] = $k . ': ' . $value;
        }
        
        $view->registerJs('console.log($("#' . $this->getId() . '")); $("#' . $this->getId() . '").joyride({
             '.  implode(', ', $joyride_options_strings).'
        });');
    }

    public function publishAndRegisterFile($file, $options = []) {
        $publish_info = Yii::$app->getAssetManager()->publish($file);

        if(isset($publish_info[1])) {
            $this->getView()->registerJsFile($publish_info[1], $options);
        }
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

            $header = Html::tag('h4', $item['header']);
            $content = Html::tag('p', $item['content']);
            
            $collection[] = Html::tag('li', $header . $content, $options);
        }
        return implode('', $collection);
    }

}
