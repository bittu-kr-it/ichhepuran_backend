<?php

namespace Database\Seeders;

use App\Models\AboutHero;
use App\Models\AboutIntro;
use App\Models\AboutMilestone;
use App\Models\GeographicReach;
use App\Models\SectionHeading;
use App\Models\TeamMember;
use App\Models\TrustBadge;
use Illuminate\Database\Seeder;

/**
 * Seeds real content migrated from the original static site
 * (docs/raw-site-content.md), not placeholder text. Note: the About page's
 * original Vision copy contained flagged bad language ("...a self-sustaining
 * cycle of luxury and nature") — per the site audit's own recommendation,
 * this was replaced with the Home page's cleaner vision wording instead of
 * migrating it verbatim. Mission text is the original About-page copy
 * (no issues flagged there).
 * Run with: php artisan db:seed --class=AboutPageSeeder
 */
class AboutPageSeeder extends Seeder
{
    public function run(): void
    {
        AboutHero::current()->update([
            'headline' => 'Our Roots in Resilience',
            'subheading' => 'Born from the tides of necessity, Ichhe Puran stands as a beacon of sustainable hope for the coastal communities of India.',
        ]);

        AboutIntro::current()->update([
            'origin_title' => 'Rising from Cyclone Yaas',
            'origin_body' => 'In May 2021, Cyclone Yaas devastated the coastlines of West Bengal and Odisha. What began as a spontaneous relief effort by a group of passionate individuals quickly transformed into a lifelong commitment. We witnessed firsthand the fragility of life in the Sundarbans. Ichhe Puran was founded not just to provide immediate aid, but to build lasting bridges toward economic independence and ecological security.',
            'established_year' => 2021,
            'vision' => 'To create a world where every community thrives in harmony with a restored environment, where green canopies shelter every home and education is the birthright of every child.',
            'mission' => 'To empower vulnerable coastal communities through climate-resilient livelihoods, education, and direct environmental stewardship.',
            'origin_image_alt' => 'Cyclone relief volunteers distributing aid in the Sundarbans after Cyclone Yaas.',
        ]);

        $reach = [
            ['state' => 'West Bengal', 'region' => 'South 24 Parganas', 'description' => 'Primary operational area — cyclone relief, agroforestry, pond restoration, school plantations, Miyawaki forest, medical camps.', 'order' => 1],
            ['state' => 'West Bengal', 'region' => 'Purulia', 'description' => 'Khushir Pujo events, farmer plant distributions at Dhundikha village.', 'order' => 2],
            ['state' => 'West Bengal', 'region' => 'Purba Medinipur', 'description' => 'Tree plantation and community outreach.', 'order' => 3],
            ['state' => 'West Bengal', 'region' => 'Kolkata', 'description' => 'Newtown Miyawaki Forest, KKR Runs to Root (Behala and Dakshineswar), lassi distribution drive.', 'order' => 4],
            ['state' => 'West Bengal', 'region' => 'Falta', 'description' => 'Sadhan Chandra Mahavidyalaya plantation — 950 trees.', 'order' => 5],
            ['state' => 'West Bengal', 'region' => 'Sundarbans', 'description' => 'Mangrove restoration — 1,500 trees.', 'order' => 6],
            ['state' => 'Jharkhand', 'region' => 'East Singhbhum', 'description' => 'Agroforestry and school plantations, Jadugora and Potka blocks.', 'order' => 7],
            ['state' => 'Odisha', 'region' => 'Rourkela, Balangir, Kalahandi, Nuapada', 'description' => 'Community outreach and plantation drives.', 'order' => 8],
        ];
        foreach ($reach as $r) {
            GeographicReach::updateOrCreate(['state' => $r['state'], 'region' => $r['region']], $r);
        }

        $milestones = [
            ['year' => '2021', 'title' => 'Foundation in Crisis', 'description' => 'Immediate relief for 10,000+ families during Cyclone Yaas. Official registration of Ichhe Puran.', 'order' => 1],
            ['year' => '2023', 'title' => 'Livelihood Programs', 'description' => 'Launched the "Green Craft" initiative, providing sustainable jobs to 500+ coastal women.', 'order' => 2],
            ['year' => '2025', 'title' => 'Mangrove Restoration', 'description' => 'Achieved the milestone of planting 1 million mangrove saplings along the delta edges.', 'order' => 3],
        ];
        foreach ($milestones as $m) {
            AboutMilestone::updateOrCreate(['year' => $m['year'], 'title' => $m['title']], $m);
        }

        $team = [
            ['name' => 'Gargee Das Mondal', 'role' => 'Social Work', 'photo_alt' => 'Portrait of Gargee Das Mondal, Social Work.', 'order' => 1],
            ['name' => 'Netai Mondal', 'role' => 'Post Treasurer & Senior Operations Manager', 'photo_alt' => 'Portrait of Netai Mondal, Post Treasurer & Senior Operations Manager.', 'order' => 2],
        ];
        foreach ($team as $t) {
            TeamMember::updateOrCreate(['name' => $t['name']], $t);
        }

        $badges = [
            ['name' => '80G Certified', 'description' => 'Tax Exemption Benefits', 'order' => 1],
            ['name' => 'FCRA Registered', 'description' => 'International Standards', 'order' => 2],
            ['name' => 'CSR Form 1', 'description' => 'Corporate Compliance', 'order' => 3],
            ['name' => 'Platinum Seal', 'description' => 'GuideStar Transparency', 'order' => 4],
        ];
        foreach ($badges as $b) {
            TrustBadge::updateOrCreate(['name' => $b['name']], $b);
        }

        $sectionHeadings = [
            'geographic-reach' => ['eyebrow' => 'Where we work', 'heading' => 'Our Geographic Reach'],
            'about-milestones' => ['eyebrow' => 'Our journey', 'heading' => 'Milestones of Impact'],
            'team' => ['eyebrow' => 'The people behind it', 'heading' => 'Meet Our Team'],
            'advisory-board' => ['eyebrow' => 'Guiding our mission', 'heading' => 'Advisory Board'],
            'trust-badges' => ['eyebrow' => 'Transparency & trust', 'heading' => 'Certified & Accountable'],
        ];
        foreach ($sectionHeadings as $key => $s) {
            SectionHeading::forKey($key, $s['eyebrow'], $s['heading'])
                ->update(['eyebrow' => $s['eyebrow'], 'heading' => $s['heading']]);
        }
    }
}
