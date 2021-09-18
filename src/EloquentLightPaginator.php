<?php

namespace fjourneau\SlimUtilities;


/**
 * Description of EloquentLightPaginator
 * Simple class to paginate Eloquent data.
 * ---------------------------------------
 * Notice that Slim 3 and slim-twig are  
 * required to use this class.
 *
 * @author fJourneau
 */
class EloquentLightPaginator {

    /**
     * Item per page (default value = 10)
     * @var type int
     */
    public static $itemPerPage = 10;
    
     public static function set_numItemPerPage($itemPerPage){
        if (is_int($itemPerPage) && $itemPerPage > 0) {
            self::$itemPerPage = $itemPerPage;
        }
    }

    public static function paginate(\Slim\Views\Twig $view, $filteredModel, $currentPage = ''){
        /* Retrieve current page */
        if ($currentPage == '' ) {
            $page = isset($_GET['page']) ? $_GET['page'] : 1;

        }else{
            $page = 1;
        }

        $page = (int)$page;
        if (!$page > 0) {
            $page = 1;
        }

        /* Pagination properties */
        $limit     = self::$itemPerPage; // Number of items on one page (default 10)
        $skip      = ($page - 1) * $limit;
        $count     = $filteredModel->count();

        $pagination = [
            'needed'        => $count > $limit,
            'count'         => $count,
            'page'          => $page,
            'lastpage'      => (ceil($count / $limit) == 0 ? 1 : ceil($count / $limit)),
            'limit'         => $limit,
            ];  

        /* Add pagination to Twig environment */
        $view->getEnvironment()->addGlobal('pagination', $pagination);

        /* Return paginated model */
        return $filteredModel->skip($skip)->take($limit)->get();

    }


}