<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SectionHeadingResource;
use App\Models\SectionHeading;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SectionHeadingController extends Controller
{
    // Known keys and their fallback defaults if an admin hasn't edited them
    // yet — keep in sync with the SectionHeadingWidget instances that
    // register each key (see each Resource's ListRecords page).
    private const DEFAULTS = [
        'pillars' => ['eyebrow' => 'What we do', 'heading' => 'Our Core Pillars'],
        'testimonials' => ['eyebrow' => 'Voices of impact', 'heading' => 'Real stories from the communities we serve'],
        'geographic-reach' => ['eyebrow' => 'Where we work', 'heading' => 'Our Geographic Reach'],
        'about-milestones' => ['eyebrow' => 'Our journey', 'heading' => 'Milestones of Impact'],
        'team' => ['eyebrow' => 'The people behind it', 'heading' => 'Meet Our Team'],
        'advisory-board' => ['eyebrow' => 'Guiding our mission', 'heading' => 'Advisory Board'],
        'trust-badges' => ['eyebrow' => 'Transparency & trust', 'heading' => 'Certified & Accountable'],
        'impact-milestones' => ['eyebrow' => 'Our journey', 'heading' => 'Journey of Impact'],
        'impact-testimonials' => ['eyebrow' => 'Voices of impact', 'heading' => 'Faces of Impact'],
        'sdg-alignment' => ['eyebrow' => 'Global commitments', 'heading' => 'Aligned with the UN Sustainable Development Goals'],
        'csr-synergy' => ['eyebrow' => 'Partner with purpose', 'heading' => 'Corporate Social Synergy'],
        'gallery-items' => ['eyebrow' => 'Moments from the field', 'heading' => 'Our Gallery'],
        'donation-methods' => ['eyebrow' => 'Ways to give', 'heading' => 'Choose How to Give'],
        'volunteer' => ['eyebrow' => 'Give your time', 'heading' => 'Volunteer With Us'],
        'csr-partnership' => ['eyebrow' => 'Partner with purpose', 'heading' => 'CSR & Corporate Partnerships'],
        // Partners logo wall (Home + About) — only `heading` is rendered,
        // the `eyebrow` is unused for these two.
        'partners-partnership' => ['eyebrow' => 'Partners', 'heading' => 'In partnership with'],
        'partners-implemented-for' => ['eyebrow' => 'Partners', 'heading' => 'Project implemented for'],
    ];

    // GET /api/v1/section-headings/{key}
    public function show(string $key)
    {
        if (! isset(self::DEFAULTS[$key])) {
            throw new NotFoundHttpException();
        }

        $defaults = self::DEFAULTS[$key];

        return new SectionHeadingResource(
            SectionHeading::forKey($key, $defaults['eyebrow'], $defaults['heading'])
        );
    }
}
