# yii2-joyride
Yii2 Joyride Wrapper is a Yii2 wrapper for the Joyride plugin. (https://github.com/zurb/joyride)

Usage:

```php
echo Joyride::widget([
          'name' => 'my-joyride',
          'joyride_options' => [
              'tipLocation' => Joyride::POS_TOP,
          ],
          'items' => [
              [
                  'header' => 'my header',
                  'content' => 'my content',
                  'target_id' => 'test',
                  'options' => [
                      'data' => [
                          'options' => 'tipLocation: bottom',
                          'prev-text' => 'Go Back',
                          'button' => 'End'
                      ]
                  ]
              ]
          ],
      ]);
```
