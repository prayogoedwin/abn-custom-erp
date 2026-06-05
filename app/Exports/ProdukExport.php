<?php

namespace App\Exports;

use App\Models\Produk;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProdukExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Produk::with('kategori')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Produk',
            'Kategori Produk',
            'Satuan',
            'Harga Basis Pembelian',
            'Harga Basis Penjualan',
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
        $produk->load('kategori'); // Pastikan relasi kategori sudah dimuat
        // dd($produk);
        return [
            $produk->id,
            $produk->nama_produk,
            $produk->kategori->nama,
            $produk->satuan,
            str_replace('.', ',', number_format($produk->harga_basis_pembelian, 0, ',', '.')),
            str_replace('.', ',', number_format($produk->harga_basis_penjualan, 0, ',', '.')),
            $produk->stok_akhir,
            $produk->isactive ? 'Active' : 'Inactive',
            $produk->created_at->format('d-m-Y H:i:s'),
            $produk->created_by,
            $produk->updated_at?->format('d-m-Y H:i:s'),
            $produk->updated_by,
            $produk->deleted_at?->format('d-m-Y H:i:s'),
            $produk->deleted_by,
        ];
    }
}
