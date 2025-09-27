<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('industrytype')->nullable()->after('tags');
            $table->string('current_employer')->nullable()->after('designation');
            $table->decimal('current_salary', 12, 2)->nullable()->after('current_employer');
            $table->string('education')->nullable()->after('current_salary');
            $table->text('key_skills')->nullable()->after('education');
            $table->date('date_of_birth')->nullable()->after('key_skills');
            $table->integer('age')->nullable()->after('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('age');
            $table->string('address1')->nullable()->after('gender');
            $table->string('address2')->nullable()->after('address1');
            $table->string('address3')->nullable()->after('address2');
            $table->string('city')->nullable()->after('address3');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->nullable()->after('state');
            $table->string('pincode', 20)->nullable()->after('country');
            $table->string('category')->nullable()->after('pincode');
            $table->string('sub_category')->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn([
                'industrytype',
                'current_employer',
                'current_salary',
                'education',
                'key_skills',
                'date_of_birth',
                'age',
                'gender',
                'address1',
                'address2',
                'address3',
                'city',
                'state',
                'country',
                'pincode',
                'category',
                'sub_category',
            ]);
        });
    }
};
