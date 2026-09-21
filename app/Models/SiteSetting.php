<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SiteSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    /**
     * Curated set — lucide-react (this project's general icon library, see
     * frontend/CLAUDE.md) dropped all brand/logo icons, so social links use
     * this separate small icon set instead (frontend/src/components/ui/SocialIcon.tsx),
     * same "fixed Select, not free text" convention as ImpactStat.icon/CsrFeature.icon.
     */
    public const SOCIAL_ICON_OPTIONS = [
        'Facebook' => 'Facebook',
        'Instagram' => 'Instagram',
        'LinkedIn' => 'LinkedIn',
        'Twitter' => 'Twitter / X',
        'YouTube' => 'YouTube',
        'WhatsApp' => 'WhatsApp',
        'Telegram' => 'Telegram',
        'Pinterest' => 'Pinterest',
        'Website' => 'Generic website',
    ];

    protected $fillable = [
        'org_name', 'logo_alt', 'tagline', 'phone', 'email', 'address',
        'social_links', 'nav_links', 'donate_href',
        'whatsapp_enabled', 'whatsapp_number', 'whatsapp_message',
        'ebook_enabled', 'ebook_label',
        'call_enabled', 'call_number',
    ];

    protected $casts = [
        'social_links' => 'array',
        'nav_links' => 'array',
        'whatsapp_enabled' => 'boolean',
        'ebook_enabled' => 'boolean',
        'call_enabled' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile()->useDisk('public');
        $this->addMediaCollection('ebook')->singleFile()->useDisk('public');
    }

    /**
     * Always fetch (and lazily create) the single settings row — there is
     * only ever one, edited from a Filament settings page, not a list.
     * Assumes `php artisan migrate` has been run, like every other content
     * type in this project — no runtime schema patching here.
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'org_name' => 'Ichhe Puran',
            'logo_alt' => 'Ichhe Puran logo',
            'tagline' => 'Nurturing nature, restoring ecosystems, and empowering communities through transparent philanthropy.',
            'phone' => '',
            'email' => '',
            'address' => '',
            // Frontend's SiteSettings type requires a string and its Navbar
            // renders this straight into a <Link href> — never leave it null.
            'donate_href' => '/get-involved#donate',
            'nav_links' => [
                ['label' => 'Home', 'href' => '/'],
                ['label' => 'About', 'href' => '/about'],
                ['label' => 'Initiatives', 'href' => '/initiatives'],
                ['label' => 'Impact', 'href' => '/impact'],
                ['label' => 'Gallery', 'href' => '/gallery'],
                ['label' => 'Contact', 'href' => '/contact'],
            ],
        ]);
    }
}
