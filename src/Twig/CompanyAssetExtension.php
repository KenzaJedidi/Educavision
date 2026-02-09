<?php

namespace App\Twig;

use Symfony\Bridge\Twig\Attribute\AsTwigExtension;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

#[AsTwigExtension]
class CompanyAssetExtension extends AbstractExtension
{
    public function __construct(private readonly KernelInterface $kernel)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('company_asset', [$this, 'companyAsset']),
            new TwigFunction('company_banner', [$this, 'companyBanner'])
        ];
    }

    /**
     * Returns a relative asset path for the company's image if it exists in public/.
     * Tries common folders and extensions. Returns null if not found.
     */
    public function companyAsset(?string $name): ?string
    {
        if (!$name) {
            return null;
        }

        $slug = $this->slugify($name);
        $folders = [
            'front-assets/images/partners',
            'front-assets/images/offers',
            'assets/images/partners',
        ];
        $exts = ['png', 'jpg', 'jpeg', 'svg', 'webp'];

        $projectDir = $this->kernel->getProjectDir();
        foreach ($folders as $folder) {
            foreach ($exts as $ext) {
                $rel = $folder . '/' . $slug . '.' . $ext;
                $path = $projectDir . '/public/' . $rel;
                if (is_file($path)) {
                    return $rel;
                }
            }
        }

        return null;
    }

        /**
         * Generates a creative banner as a data URL with brand-aware gradients and shapes.
         */
        public function companyBanner(?string $name, ?string $subtitle = null): string
        {
                $title = $name ? $name : '';
                $slug = $this->slugify($title);
                [$c1, $c2] = $this->pickPalette($slug);
                $titleUpper = $title !== '' ? mb_strtoupper($title) : ($subtitle ?? '');
                $sub = $subtitle ?: 'Offre de stage';

                $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 300">
    <defs>
        <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="$c1"/>
            <stop offset="100%" stop-color="$c2"/>
        </linearGradient>
        <filter id="softShadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#000" flood-opacity="0.2"/>
        </filter>
    </defs>
    <rect width="1200" height="300" rx="12" fill="url(#g)"/>
    <circle cx="1040" cy="60" r="90" fill="rgba(255,255,255,0.20)"/>
    <circle cx="180" cy="260" r="120" fill="rgba(255,255,255,0.10)"/>
    <path d="M0 220 Q 300 180 600 220 T 1200 220 V 300 H 0 Z" fill="rgba(255,255,255,0.15)"/>
    <text x="40" y="110" font-family="Inter, Arial, sans-serif" font-size="88" font-weight="700" fill="#fff" filter="url(#softShadow)">$titleUpper</text>
    <text x="40" y="160" font-family="Inter, Arial, sans-serif" font-size="26" font-weight="600" fill="rgba(255,255,255,0.95)">$sub</text>
</svg>
SVG;

                return 'data:image/svg+xml,' . rawurlencode($svg);
        }

    private function slugify(string $text): string
    {
        $text = trim($text);
        if (function_exists('transliterator_transliterate')) {
            $text = transliterator_transliterate('Any-Latin; Latin-ASCII', $text);
        }
        $text = mb_strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
        $text = trim($text, '-');
        return $text !== '' ? $text : 'logo';
    }

    private function pickPalette(string $slug): array
    {
        $brands = [
            'ey' => ['#F2C94C', '#F2994A'],
            'orange' => ['#F2994A', '#F2C94C'],
            'actia' => ['#2F80ED', '#56CCF2'],
            'esprit' => ['#F2C94C', '#F2B70D'],
            'bnp-paribas' => ['#2ECC71', '#27AE60'],
        ];
        if (isset($brands[$slug])) {
            return $brands[$slug];
        }

        $defaults = [
            ['#2F80ED', '#56CCF2'],
            ['#6FCF97', '#27AE60'],
            ['#F2994A', '#EB5757'],
            ['#BB6BD9', '#6A5ACD'],
            ['#F2C94C', '#F2994A'],
        ];
        $idx = crc32($slug) % count($defaults);
        return $defaults[$idx];
    }
}
