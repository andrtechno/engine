<?php

namespace panix\engine\web;


use Yii;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\web\DbSession as Session;
use panix\engine\CMS;

/**
 * Class DbSession
 * @package panix\engine\web
 */
class DbSession extends Session
{

    public $writeCallback = ['panix\engine\web\DbSession', 'writeFields'];
    public $sessionTable = '{{%session}}';


    public static function writeFields($session)
    {

        try {
            $uid = (Yii::$app->user->getIdentity(false) == null) ? null : Yii::$app->user->getIdentity(false)->id;
            $ip = Yii::$app->request->getRemoteIP();
            if (Yii::$app->user->getIsGuest()) {
                $checkBot = CMS::isBot();
                if ($checkBot['success']) {
                    $user_name = substr($checkBot['name'], 0, 25);
                    $user_type = 'SearchBot';
                } else {
                    $user_name = $ip;
                    $user_type = 'Guest';
                }
            } else {
                $user_type = 'User';
                $user_name = Yii::$app->user->username;
            }


            $data = [];
            $data['user_id'] = $uid;
            $data['ip'] = $ip;

            if(Yii::$app->user->getIsGuest()){
               // $data['expire_start'] = time();
            }
            $data['user_type'] = $user_type;
            $data['user_name'] = $user_name;
            return $data;

        } catch (InvalidConfigException $e) {
            echo 'session error panix\engine\web\DbSession';
            \Yii::info(print_r($e));
        }
    }

    public function writeSession2($id, $data)
    {
        // exception must be caught in session write handler
        // https://secure.php.net/manual/en/function.session-set-save-handler.php#refsect1-function.session-set-save-handler-notes
        try {
            // ensure backwards compatability (fixed #9438)
            if ($this->writeCallback && !$this->fields) {
                $this->fields = $this->composeFields();
            }
            // ensure data consistency
            if (!isset($this->fields['data'])) {
                $this->fields['data'] = $data;
            } else {
                $_SESSION = $this->fields['data'];
            }
            // ensure 'id' and 'expire' are never affected by [[writeCallback]]
            $this->fields = array_merge($this->fields, [
                'id' => $id,
                'expire' => time() + $this->getTimeout(),
            ]);
            $this->fields = $this->typecastFields($this->fields);

            $this->db->createCommand()->upsert($this->sessionTable, $this->fields)->execute();
            $this->fields = [];
        } catch (\Exception $e) {
            Yii::$app->errorHandler->handleException($e);
            return false;
        }
        return true;
    }

    public function regenerateID2($deleteOldSession = false)
    {
        $oldID = session_id();

        // if no session is started, there is nothing to regenerate
        if (empty($oldID)) {
            return;
        }

        parent::regenerateID(false);
        $newID = session_id();
        // if session id regeneration failed, no need to create/update it.
        if (empty($newID)) {
            Yii::warning('Failed to generate new session ID', __METHOD__);
            return;
        }

        $row = $this->db->useMaster(function() use ($oldID) {
            return (new Query())->from($this->sessionTable)
                ->where(['id' => $oldID])
                ->createCommand($this->db)
                ->queryOne();
        });

        if ($row !== false) {
            if ($deleteOldSession) {
                $this->db->createCommand()
                    ->update($this->sessionTable, ['id' => $newID], ['id' => $oldID])
                    ->execute();
            } else {
                die('inser1');
                $row['id'] = $newID;
                $row['expire_start'] = time();
                $this->db->createCommand()
                    ->insert($this->sessionTable, $row)
                    ->execute();
            }
        } else {
            die('inser2');
            // shouldn't reach here normally
            $this->fields['expire_start'] = time();
            $this->db->createCommand()
                ->insert($this->sessionTable, $this->composeFields($newID, ''))
                ->execute();
        }
    }

}
