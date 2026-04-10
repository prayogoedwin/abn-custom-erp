<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProdukExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::with('roles')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Produk',
            'Kategori Produk',
            'Satuan',
            'Harga Basis Pembelian',
            'Stok Akhir',
            'Status',
            'Created At',
            'Created By',
            'Updated At',
            'Updated By',
            'Deleted At',
            'Deleted By',
        ];
    }

    public function map($produk): array
    {
        return [
            $produk->id,
            $produk->nama_produk,
            $produk->kategori_produk->name,
            $produk->satuan,
            $produk->harga_basis_pembelian,
            $produk->stok_akhir,
            $produk->isactive ? 'Active' : 'Inactive',
            $produk->created_at->format('Y-m-d H:i:s'),
            $produk->created_by,
            $produk->updated_at?->format('Y-m-d H:i:s'),
            $produk->updated_by,
            $produk->deleted_at?->format('Y-m-d H:i:s'),
            $produk->deleted_by,
        ];
    }
}
