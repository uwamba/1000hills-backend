
    <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToPaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('currency_code')->default('USD')->after('amount_paid');
            $table->decimal('currency_rate_to_usd', 15, 6)->default(1.0)->after('currency_code');
            $table->string('payment_method')->nullable()->after('currency_rate_to_usd');
            $table->text('extra_note')->nullable()->after('payment_method');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'currency_code',
                'currency_rate_to_usd',
                'payment_method',
                'extra_note',
            ]);
        });
    }

};
