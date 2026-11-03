
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $now = Carbon::now();

            // Ambil ID pembelian yang sudah di-soft delete
            $pembelianIds = DB::table('pembelians')
                ->whereNotNull('deleted_at')
                ->pluck('id');

            if ($pembelianIds->isEmpty()) {
                return;
            }

            // Ambil ID detail pembelian terkait
            $detailIds = DB::table('pembelian_details')
                ->whereIn('pembelian_id', $pembelianIds)
                ->whereNull('deleted_at')
                ->pluck('id');

            if ($detailIds->isEmpty()) {
                return;
            }

            // Soft delete detail pembelian
            DB::table('pembelian_details')
                ->whereIn('id', $detailIds)
                ->update([
                    'deleted_at' => $now,
                    'updated_at' => $now,
                ]);

            // Soft delete stok terkait
            DB::table('stoks')
                ->whereIn('pembelian_detail_id', $detailIds)
                ->delete();

            
        });
    }

    public function down(): void
    {
        
    }
};