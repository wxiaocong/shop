<?php namespace App\Utils;

use Illuminate\Support\Collection;

/**
 * Page Utils Model
 *
 * @author Jakty Ling(lingjun@carnetmotor.com)
 */
class Page
{
    const PAGESIZE = 10;

    private $curPage     = 1;
    private $totalRecord = 1;
    private $totalPage   = 1;
    private $startIndex  = 1;
    private $endIndex    = 10;

    private $data        = array();
    private $pageNumbers = array();

    /**
     * Get the signature of the data.
     *
     * @param   Collection $data
     * @return  void
     */
    public function __construct(
        $data = null,
        $paginationNumber = 10
    ) {
        if (isset($data)) {
            $this->curPage     = $data->currentPage();
            $this->totalPage   = $data->lastPage();
            $this->totalRecord = $data->total();
            $this->startIndex  = $data->firstItem();
            $this->endIndex    = $data->lastItem();

            if ($this->curPage > intval($paginationNumber / 2)) {
                $startPage = $this->curPage - intval($paginationNumber / 2);
            } else {
                $startPage = 1;
            }

            $endPage = $startPage + $paginationNumber - 1;

            if ($endPage > $this->totalPage) {
                $endPage = $this->totalPage;
            }

            for ($i = 0; $startPage <= $endPage; $startPage++, $i++) {
                $this->pageNumbers[$i] = $startPage;
            }

            $this->data = $data;
        }
    }

    /**
     * Get the value of property.
     *
     * @param   string $propertyName
     *
     * @return  property value
     */
    public function __get($propertyName)
    {
        if (isset($this->$propertyName)) {
            return ($this->$propertyName);
        } else {
            return (null);
        }
    }

    /**
     * Set the value of property.
     *
     * @param   string $propertyName
     * @param   Object $value
     *
     * @return  property value
     */
    public function __set($propertyName, $value)
    {
        $this->$propertyName = $value;
    }
}
