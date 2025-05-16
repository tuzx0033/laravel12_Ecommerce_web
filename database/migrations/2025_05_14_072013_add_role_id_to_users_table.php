<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddRoleIdToUsersTable extends Migration
{
    public function up()
    {
        // Kiểm tra xem cột role_id đã tồn tại chưa
        if (!Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->nullable();
            });
        }

        // Đảm bảo vai trò mặc định tồn tại
        if (class_exists(\App\Models\Role::class)) {
            $defaultRole = \App\Models\Role::firstOrCreate(['name' => 'user'], ['name' => 'user']);
            // Cập nhật role_id không hợp lệ hoặc NULL thành role_id mặc định
            if (class_exists(\App\Models\User::class)) {
                DB::table('users')
                    ->whereNull('role_id')
                    ->orWhereNotIn('role_id', DB::table('roles')->pluck('id'))
                    ->update(['role_id' => $defaultRole->id]);
            }
        } else {
            throw new \Exception('Model Role không tồn tại. Vui lòng tạo model Role trước khi chạy migration.');
        }

        // Thêm ràng buộc khóa ngoại nếu chưa có
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                try {
                    $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Bỏ qua nếu khóa ngoại đã tồn tại
                    if (strpos($e->getMessage(), 'SQLSTATE[HY000]: General error: 1215') !== false ||
                        strpos($e->getMessage(), 'SQLSTATE[23000]') !== false) {
                        // Khóa ngoại có thể đã tồn tại
                    } else {
                        throw $e;
                    }
                }
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropForeign(['role_id']);
            } catch (\Exception $e) {
                // Bỏ qua nếu khóa ngoại không tồn tại
            }
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropColumn('role_id');
            }
        });
    }
}
