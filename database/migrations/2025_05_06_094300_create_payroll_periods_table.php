<?php

use App\Enums\PayrollStatus;
use App\Enums\PayrollType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();

            $table->string("month");
            $table->string("year");
            $table->string("employment_type");
            $table->string("payroll_type")->default(PayrollType::GENERAL);
            $table->string("period_type");

            $table->integer('period_start');
            $table->integer('period_end');

            $table->integer("days_of_duty")->default(22);
            $table->string("status")->default(PayrollStatus::INACTIVE);
            $table->boolean('is_active')->default(false);

            $table->dateTime("posted_at")->nullable();
            $table->dateTime("locked_at")->nullable();
            $table->dateTime("last_generated_at")->nullable();


            $table->softDeletes();
            $table->timestamps();

            $table->unique(['month', 'year', 'employment_type', 'payroll_type'], 'payroll_period_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payroll_periods');
    }
}
