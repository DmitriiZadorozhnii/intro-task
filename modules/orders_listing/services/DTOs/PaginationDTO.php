<?php

namespace app\modules\orders_listing\services\DTOs;

class PaginationDTO
{
    public function __construct(
        protected int $page,
        protected int $perPage,
        protected int $totalCount,
        protected int $totalPages,
    ) {
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }
}
