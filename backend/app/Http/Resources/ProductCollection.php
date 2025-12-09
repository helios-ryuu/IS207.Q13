<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        // Kiểm tra xem dữ liệu truyền vào có phải là Paginator không
        if ($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return [
                'data' => $this->collection,
                'meta' => [
                    'total' => $this->total(),
                    'count' => $this->count(),
                    'per_page' => $this->perPage(),
                    'current_page' => $this->currentPage(),
                    'total_pages' => $this->lastPage()
                ],
            ];
        }

        // Nếu là Collection thường (dùng ->get())
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->count(),
                'count' => $this->count(),
                'per_page' => $this->count(),
                'current_page' => 1,
                'total_pages' => 1
            ],
        ];
    }
}