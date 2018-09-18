<?php
/* @var $className string the new migration class name without namespace */
/* @var $namespace string the new migration class namespace */

$config = Yii::$app->settings->get('app');
$date = new \DateTime('now');
$date->setTimezone(new \DateTimeZone((isset($config->timezone)) ? $config->timezone : date_default_timezone_get()));

echo "<?php\n";
if (!empty($namespace)) {
    echo "\nnamespace {$namespace};\n";
}
?>
/**
 * Generation migrate by <?= Yii::$app->name . "\n" ?>
 *
 * @author <?= Yii::$app->name ?> development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua <?= Yii::$app->name . "\n" ?>
 *
 * Class <?= $className . "\n" ?>
 */

use yii\db\Schema;
use panix\engine\db\Migration;

class <?= $className ?> extends Migration {

    // Use up()/down() to run migration code without a transaction.
    public function up() {

    }

    public function down() {
        echo "<?= $className ?> cannot be reverted.\n";

        return false;
    }

}
