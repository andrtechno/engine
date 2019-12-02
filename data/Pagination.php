<?php

namespace panix\engine\data;

use Yii;
use yii\web\Request;

/**
 * Class Pagination
 * @package panix\engine\data
 */
class Pagination extends \yii\data\Pagination
{
    const LINK_NEXT = 'next2';
    const LINK_PREV = 'prev2';
    const LINK_FIRST = 'first2';
    const LINK_LAST = 'last2';


    public function createUrl($page, $pageSize = null, $absolute = false)
    {

        $page = (int)$page;
        $pageSize = (int)$pageSize;
        if (($params = $this->params) === null) {
            $request = Yii::$app->getRequest();
            $params = $request instanceof Request ? $request->getQueryParams() : [];
        }
        if ($page > 0 || $page == 0 && $this->forcePageParam) {
            $params[$this->pageParam] = $page + 1;
        } else {
            unset($params[$this->pageParam]);
        }
        if ($pageSize <= 0) {
            $pageSize = $this->getPageSize();
        }
        if ($pageSize != $this->defaultPageSize) {
            $params[$this->pageSizeParam] = $pageSize;
        } else {
            unset($params[$this->pageSizeParam]);
        }
        $params[0] = $this->route === null ? Yii::$app->controller->getRoute() : $this->route;
        $urlManager = $this->urlManager === null ? Yii::$app->getUrlManager() : $this->urlManager;
        if (Yii::$app->request->isPjax && isset($params['_pjax'])) {
            unset($params['_pjax']);
        }
        if ($absolute) {
            return $urlManager->createAbsoluteUrl($params);
        }

        return $urlManager->createUrl($params);
    }

}
