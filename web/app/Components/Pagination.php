<?php

namespace Components;


class Pagination
{
    private static $_model = null;

    private static $_size = 5;

    private static $_pageGroup = 7;

    /**
     * count
     pages
     hasPrevious
     hasNext
     current
     *
     */
    private static $_page = null;



    public static function getPage($sql, $model, $options = [])
    {
        self::$_model = $model;

        self::$_page = new \StdClass;

        self::$_page->current = self::_getCurrent();


        $bind = [];

        if (! empty($options['bind'])) {
            $bind = $options['bind'];
        }

        $counts = self::_getCounts($sql, $bind);


        self::$_page->count = $counts['res'];
        self::$_page->pages = self::_getPageCounts($counts['page']);

        self::$_page->start = (self::$_page->current - 1) * self::$_size + 1;
        self::$_page->end = self::$_page->current * self::$_size;

        if (self::$_page->end > $counts['res']) {
            self::$_page->end = $counts['res'];
        }

        self::$_page->previous = self::_hasPrevious();
        self::$_page->next = self::_hasNext($counts['page']);

        return [
            '_pageHtml' => self::_getPageHtml(self::$_page),
            '_res' => self::_getPageRes($sql, $bind),
        ];
    }

    private static function _getPageRes($sql, $bind)
    {
        if (self::$_page->start < 1) {
            return [];
        }

        $sql = sprintf('%s limit %s,%s', $sql, self::$_page->start - 1, self::$_size);

        $stmt = self::$_model->execute($sql, $bind);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * get the count of page and result
     * @param sql
     * @param bind
     *
     * @return
     */
    private static function _getCounts($sql, $bind)
    {
        $stmt = self::$_model->execute($sql, $bind);

        $counts = [];

        $counts['res']  = $stmt->fetchColumn();
        $counts['page'] = intval(ceil($counts['res'] / self::$_size));

        return $counts;
    }

    /**
     * check if have previous page
     *
     * @return
     */
    private static function _hasPrevious()
    {
        return self::$_page->current - 1;
    }

    /**
     * check if have next page
     * @param pageCount
     *
     * @return
     */
    private static function _hasNext($pageCount)
    {
        if ($pageCount > self::$_page->current) {
            return self::$_page->current + 1;
        }

        return 0;
    }

    /**
     * get current page number
     *
     * @return
     */
    private static function _getCurrent()
    {
        $current = intval(\Afanty\Web\Request\Http::get('p'));

        if ($current > 0) {
            return $current;
        }

        return 1;
    }

    private static function _getPageCounts($pageCount)
    {
        $count = [];
        $left = [];
        $right = [];

        //get left group
        $halfGroup = intval((self::$_pageGroup - 1) / 2);

        if (self::$_page->current > $pageCount) {
            self::$_page->current = $pageCount;
        }

        if ($halfGroup >= self::$_page->current) {
            $left = range(1, self::$_page->current, 1);
        } else {
            $left = range((self::$_page->current - $halfGroup), self::$_page->current, 1);
        }

        array_pop($left);

        //get right group
        if ((self::$_page->current + $halfGroup) >= $pageCount) {
            $right = range(self::$_page->current, $pageCount, 1);
        } else {
            $right = range(self::$_page->current, self::$_page->current + $halfGroup, 1);
        }

        $count = array_merge($left, $right);

        return $count;
    }

    private static function _getPageHtml($pageObj)
    {
        if ($pageObj->start < 1 || count($pageObj->pages) <= 1) {
            return [];
        }

        $pageUrl = function($page){
            return self::_getUrl($page);
        };

        $html = <<<"EOD"
<div class="row">
  <div class="col-sm-5">
        <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing {$pageObj->start} to {$pageObj->end} of {$pageObj->count} entries</div>
  </div>
  <div class="col-sm-7">
    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
      <ul class="pagination">
EOD;
        if ($pageObj->previous) {
            $html .= <<<"EOD"
        <li class="paginate_button previous" id="example2_previous">
          <a href="{$pageUrl($pageObj->previous)}" aria-controls="example2" data-dt-idx="{$pageObj->previous}" tabindex="{$pageObj->previous}">Previous</a>
        </li>
EOD;
        }

        foreach ($pageObj->pages as $page ) {
            $active = '';
            if ($page == $pageObj->current) {
                $active = 'active';
            }
            $html.= <<<EOD
        <li class="paginate_button {$active}">
          <a href="{$pageUrl($page)}" aria-controls="example2" data-dt-idx="{$page}" tabindex="{$page}">{$page}</a>
        </li>
EOD;
        }

        if ($pageObj->next) {
            $html .= <<<"EOD"
        <li class="paginate_button next" id="example2_next">
          <a href="{$pageUrl($pageObj->next)}" aria-controls="example2" data-dt-idx="{$pageObj->next}" tabindex="{$pageObj->next}">Next</a>
        </li>
EOD;
        }

        $html .= <<<'EOD'
      </ul>
    </div>
  </div>
</div>
EOD;
        return $html;
    }

    private static function _getUrl($pageNumber)
    {
        $baseUrl = $_SERVER['REQUEST_URI'];
        $baseUrl = explode("?", $baseUrl);

        $queryArgs = $_REQUEST;
        $queryArgs['p'] = $pageNumber;

        return sprintf("%s?%s", $baseUrl[0], http_build_query($queryArgs));
    }
}
