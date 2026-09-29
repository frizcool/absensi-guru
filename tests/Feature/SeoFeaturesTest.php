<?php

namespace Tests\Feature;

use App\Models\PengaturanSekolah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        PengaturanSekolah::getSetting()->update([
            'nama_sekolah' => 'UPTD SPF SD Inpres Rappojawa',
            'npsn' => '40307321',
            'alamat' => 'Jl. Rappojawa No. 1, Makassar',
            'telepon' => '0411-123456',
            'email' => 'info@sdinpresrappojawa.sch.id',
        ]);
    }

    public function test_halaman_login_memiliki_meta_seo_lengkap_dan_schema_org(): void
    {
        $response = $this->get('/sekolahku');

        $response->assertStatus(200);

        // Title & Meta Tags
        $response->assertSee('<title>Masuk &bull; UPTD SPF SD Inpres Rappojawa - Sistem Presensi Guru Online</title>', false);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta name="keywords"', false);
        $response->assertSee('<meta name="robots" content="index, follow">', false);
        $response->assertSee('<link rel="canonical"', false);

        // Open Graph & Social Cards
        $response->assertSee('<meta property="og:type" content="website">', false);
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:image"', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);

        // Schema.org Structured Data
        $response->assertSee('https://schema.org', false);
        $response->assertSee('"@type":"School"', false);
        $response->assertSee('"@type":"WebApplication"', false);
        $response->assertSee('UPTD SPF SD Inpres Rappojawa', false);
    }

    public function test_halaman_live_display_memiliki_meta_seo(): void
    {
        $response = $this->get('/sekolahku/live-display');

        $response->assertStatus(200);
        $response->assertSee('<title>Live Display Presensi Guru &bull; UPTD SPF SD Inpres Rappojawa</title>', false);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('<meta property="og:type" content="website">', false);
        $response->assertSee('<meta property="og:image"', false);
    }

    public function test_sitemap_xml_dapat_diakses_dan_berformat_xml_valid(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $response->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false);
        $response->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false);
        $response->assertSee('<loc>', false);
        $response->assertSee('/sekolahku', false);
        $response->assertSee('<changefreq>daily</changefreq>', false);
        $response->assertSee('<priority>1.0</priority>', false);
    }

    public function test_robots_txt_mengizinkan_halaman_publik_dan_melarang_panel_internal(): void
    {
        $robotsContent = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Allow: /sekolahku', $robotsContent);
        $this->assertStringContainsString('Allow: /sekolahku/live-display', $robotsContent);
        $this->assertStringContainsString('Disallow: /guru/', $robotsContent);
        $this->assertStringContainsString('Disallow: /sekolahku/panel/', $robotsContent);
        $this->assertStringContainsString('Sitemap: /sitemap.xml', $robotsContent);
    }
}
