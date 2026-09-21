<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'whatsapp_enabled')) {
                $table->boolean('whatsapp_enabled')->default(false)->after('donate_href');
            }
            if (! Schema::hasColumn('site_settings', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable()->after('whatsapp_enabled');
            }
            if (! Schema::hasColumn('site_settings', 'whatsapp_message')) {
                $table->string('whatsapp_message')->nullable()->after('whatsapp_number');
            }
            if (! Schema::hasColumn('site_settings', 'ebook_enabled')) {
                $table->boolean('ebook_enabled')->default(false)->after('whatsapp_message');
            }
            if (! Schema::hasColumn('site_settings', 'ebook_label')) {
                $table->string('ebook_label')->nullable()->after('ebook_enabled');
            }
        });

        // Backfill an `icon` key onto any existing social_links rows saved
        // before the icon picker existed, matched by label — otherwise the
        // admin form's now-`->required()` icon Select blocks saving the
        // whole repeater until every pre-existing row is manually fixed.
        DB::table('site_settings')->whereNotNull('social_links')->get(['id', 'social_links'])->each(function ($row) {
            $links = json_decode($row->social_links, true);
            if (! is_array($links)) {
                return;
            }

            $changed = false;
            foreach ($links as &$link) {
                if (! empty($link['icon'])) {
                    continue;
                }

                $label = strtolower(trim($link['label'] ?? ''));
                $match = match (true) {
                    str_contains($label, 'facebook') => 'Facebook',
                    str_contains($label, 'instagram') => 'Instagram',
                    str_contains($label, 'linkedin') => 'LinkedIn',
                    $label === 'x', str_contains($label, 'twitter') => 'Twitter',
                    str_contains($label, 'youtube') => 'YouTube',
                    str_contains($label, 'whatsapp') => 'WhatsApp',
                    str_contains($label, 'telegram') => 'Telegram',
                    str_contains($label, 'pinterest') => 'Pinterest',
                    default => 'Website',
                };

                $link['icon'] = $match;
                $changed = true;
            }
            unset($link);

            if ($changed) {
                DB::table('site_settings')->where('id', $row->id)->update([
                    'social_links' => json_encode($links),
                ]);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        Schema::table('site_settings', function (Blueprint $table) {
            foreach (['whatsapp_enabled', 'whatsapp_number', 'whatsapp_message', 'ebook_enabled', 'ebook_label'] as $column) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
