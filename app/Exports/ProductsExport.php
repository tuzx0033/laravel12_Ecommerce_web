<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::with('category')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tên sản phẩm',
            'Mô tả',
            'Giá',
            'Danh mục',
            'Hình ảnh',
            'Ngày tạo',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->description,
            $product->price,
            $product->category ? $product->category->name : 'Không có danh mục',
            $product->image ? asset('storage/' . $product->image) : null,
            $product->created_at->format('Y-m-d H:i:s'),
        ];
    }
}