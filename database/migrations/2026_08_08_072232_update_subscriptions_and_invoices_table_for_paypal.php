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
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'amount')) {
                $table->decimal('amount', 10, 2)->default(0.00)->after('subscription_plan_id');
            }
            if (!Schema::hasColumn('subscriptions', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('amount');
            }
            if (!Schema::hasColumn('subscriptions', 'billing_cycle')) {
                $table->string('billing_cycle')->default('monthly')->after('currency');
            }
            if (!Schema::hasColumn('subscriptions', 'trial_starts_at')) {
                $table->timestamp('trial_starts_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('subscriptions', 'renews_at')) {
                $table->timestamp('renews_at')->nullable()->after('ends_at');
            }
            if (!Schema::hasColumn('subscriptions', 'last_reminder_sent_at')) {
                $table->timestamp('last_reminder_sent_at')->nullable()->after('renews_at');
            }
            if (!Schema::hasColumn('subscriptions', 'reminder_logs')) {
                $table->json('reminder_logs')->nullable()->after('last_reminder_sent_at');
            }
        });

        Schema::table('subscription_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('subscription_invoices', 'subscription_id')) {
                $table->unsignedBigInteger('subscription_id')->nullable()->after('hotel_id');
            }
            if (!Schema::hasColumn('subscription_invoices', 'subscription_plan_id')) {
                $table->unsignedBigInteger('subscription_plan_id')->nullable()->after('subscription_id');
            }
            if (!Schema::hasColumn('subscription_invoices', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('amount');
            }
            if (!Schema::hasColumn('subscription_invoices', 'notes')) {
                $table->text('notes')->nullable()->after('currency');
            }
            if (!Schema::hasColumn('subscription_invoices', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('status');
            }
            if (!Schema::hasColumn('subscription_invoices', 'paypal_order_id')) {
                $table->string('paypal_order_id')->nullable()->index()->after('payment_status');
            }
            if (!Schema::hasColumn('subscription_invoices', 'paypal_transaction_id')) {
                $table->string('paypal_transaction_id')->nullable()->index()->after('paypal_order_id');
            }
            if (!Schema::hasColumn('subscription_invoices', 'paypal_payer_email')) {
                $table->string('paypal_payer_email')->nullable()->after('paypal_transaction_id');
            }
        });

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'subscription_invoice_id')) {
                    $table->unsignedBigInteger('subscription_invoice_id')->nullable()->after('hotel_id');
                }
                if (!Schema::hasColumn('payments', 'paypal_order_id')) {
                    $table->string('paypal_order_id')->nullable()->index()->after('subscription_invoice_id');
                }
                if (!Schema::hasColumn('payments', 'paypal_transaction_id')) {
                    $table->string('paypal_transaction_id')->nullable()->index()->after('paypal_order_id');
                }
                if (!Schema::hasColumn('payments', 'payment_details')) {
                    $table->json('payment_details')->nullable()->after('paypal_transaction_id');
                }
            });
        }

        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('activity_logs', 'admin_id')) {
                    $table->unsignedBigInteger('admin_id')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('activity_logs', 'admin_name')) {
                    $table->string('admin_name')->nullable()->after('admin_id');
                }
                if (!Schema::hasColumn('activity_logs', 'previous_status')) {
                    $table->string('previous_status')->nullable()->after('admin_name');
                }
                if (!Schema::hasColumn('activity_logs', 'new_status')) {
                    $table->string('new_status')->nullable()->after('previous_status');
                }
                if (!Schema::hasColumn('activity_logs', 'notes')) {
                    $table->text('notes')->nullable()->after('new_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['amount', 'currency', 'billing_cycle', 'trial_starts_at', 'renews_at', 'last_reminder_sent_at', 'reminder_logs']);
        });

        Schema::table('subscription_invoices', function (Blueprint $table) {
            $table->dropColumn(['currency', 'notes', 'payment_status', 'paypal_order_id', 'paypal_transaction_id', 'paypal_payer_email']);
        });

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn(['subscription_invoice_id', 'paypal_order_id', 'paypal_transaction_id', 'payment_details']);
            });
        }

        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn(['admin_id', 'admin_name', 'previous_status', 'new_status', 'notes']);
            });
        }
    }
};
