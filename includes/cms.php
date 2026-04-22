<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/url.php';
require_once __DIR__ . '/cache.php';

function cms_cache_key(string $bucket, string $key): string
{
    return 'cms:' . $bucket . ':' . $key;
}

function cms_invalidate_page_cache(?string $slug = null): void
{
    if ($slug !== null && $slug !== '') {
        cache_delete(cms_cache_key('page_slug', $slug));
        return;
    }
    cache_clear_prefix('cms:page_slug:');
}

function cms_invalidate_menu_cache(?string $locationKey = null): void
{
    if ($locationKey !== null && $locationKey !== '') {
        cache_delete(cms_cache_key('menu', $locationKey));
        return;
    }
    cache_clear_prefix('cms:menu:');
}

function cms_invalidate_settings_cache(?string $settingKey = null): void
{
    if ($settingKey !== null && $settingKey !== '') {
        cache_delete(cms_cache_key('setting', $settingKey));
        return;
    }
    cache_clear_prefix('cms:setting:');
}

function cms_invalidate_home_slides_cache(): void
{
    cache_delete(cms_cache_key('home', 'slides'));
}

function cms_get_setting(string $key, ?string $default = null): ?string
{
    $cacheKey = cms_cache_key('setting', $key);
    $cached = cache_get($cacheKey);
    if ($cached !== null) {
        return (string) $cached;
    }

    $pdo = db();
    if (!$pdo) {
        return $default;
    }

    $value = db_fetch_value($pdo, 'SELECT setting_value FROM site_settings WHERE setting_key = :k LIMIT 1', [
        ':k' => $key,
    ]);

    $resolved = $value !== false ? (string) $value : $default;
    if ($resolved !== null) {
        cache_set($cacheKey, $resolved, 600);
    }

    return $resolved;
}

function cms_get_menu(string $locationKey): array
{
    $cacheKey = cms_cache_key('menu', $locationKey);
    $cached = cache_get($cacheKey);
    if (is_array($cached)) {
        return $cached;
    }

    $pdo = db();
    if (!$pdo) {
        return [];
    }

    $rows = db_fetch_all($pdo, 'SELECT mi.id, mi.parent_id, mi.title, mi.url, mi.sort_order FROM menus m INNER JOIN menu_items mi ON mi.menu_id = m.id WHERE m.location_key = :loc AND mi.is_active = 1 ORDER BY mi.sort_order ASC, mi.id ASC', [
        ':loc' => $locationKey,
    ]);
    if (!$rows) {
        return [];
    }

    $byParent = [];
    foreach ($rows as $row) {
        $parent = $row['parent_id'] ? (int) $row['parent_id'] : 0;
        $byParent[$parent][] = $row;
    }

    $build = function (int $parentId) use (&$build, $byParent): array {
        $list = [];
        foreach ($byParent[$parentId] ?? [] as $row) {
            $list[] = [
                'id' => (int) $row['id'],
                'title' => (string) $row['title'],
                'url' => (string) $row['url'],
                'children' => $build((int) $row['id']),
            ];
        }
        return $list;
    };

    $menu = $build(0);
    cache_set($cacheKey, $menu, 600);
    return $menu;
}

function cms_get_footer_sections(): array
{
    $pdo = db();
    if (!$pdo) {
        return [];
    }

    $rows = $pdo->query('SELECT fs.id, fs.title, fl.label, fl.url FROM footer_sections fs LEFT JOIN footer_links fl ON fl.section_id = fs.id ORDER BY fs.sort_order ASC, fs.id ASC, fl.sort_order ASC, fl.id ASC')->fetchAll();
    if (!$rows) {
        return [];
    }

    $result = [];
    foreach ($rows as $row) {
        $sid = (int) $row['id'];
        if (!isset($result[$sid])) {
            $result[$sid] = [
                'id' => $sid,
                'title' => (string) $row['title'],
                'links' => [],
            ];
        }

        if ($row['label'] !== null) {
            $result[$sid]['links'][] = [
                'label' => (string) $row['label'],
                'url' => (string) $row['url'],
            ];
        }
    }

    return array_values($result);
}

function get_page_by_slug(string $slug): ?array
{
    if (!validate_slug_value($slug)) {
        return null;
    }

    $cacheKey = cms_cache_key('page_slug', $slug);
    $cached = cache_get($cacheKey);
    if (is_array($cached) || $cached === false) {
        return $cached === false ? null : $cached;
    }

    $pdo = db();
    if (!$pdo) {
        return null;
    }

    $page = db_fetch_one($pdo, 'SELECT p.*, pm.meta_title, pm.meta_description, pm.meta_keywords, pm.canonical_url FROM pages p LEFT JOIN page_meta pm ON pm.page_id = p.id WHERE p.slug = :slug AND p.status = "published" LIMIT 1', [
        ':slug' => $slug,
    ]);
    if ($page) {
        cache_set($cacheKey, $page, 300);
        return $page;
    }

    cache_set($cacheKey, false, 120);
    return null;
}

function get_page_sections(int $pageId): array
{
    $pdo = db();
    if (!$pdo) {
        return [];
    }

    $rows = db_fetch_all($pdo, 'SELECT ps.id AS section_id, ps.page_id, ps.section_key, ps.title AS section_title, ps.body AS section_body, ps.sort_order AS section_sort_order, psi.id AS item_id, psi.item_key, psi.title AS item_title, psi.body AS item_body, psi.image_path, psi.link_url, psi.sort_order AS item_sort_order FROM page_sections ps LEFT JOIN page_section_items psi ON psi.section_id = ps.id WHERE ps.page_id = :page_id ORDER BY ps.sort_order ASC, ps.id ASC, psi.sort_order ASC, psi.id ASC', [
        ':page_id' => $pageId,
    ]);

    $sections = [];
    foreach ($rows as $row) {
        $sid = (int) $row['section_id'];
        if (!isset($sections[$sid])) {
            $sections[$sid] = [
                'id' => $sid,
                'page_id' => (int) $row['page_id'],
                'section_key' => (string) $row['section_key'],
                'title' => (string) ($row['section_title'] ?? ''),
                'body' => (string) ($row['section_body'] ?? ''),
                'sort_order' => (int) $row['section_sort_order'],
                'items' => [],
            ];
        }

        if ($row['item_id'] !== null) {
            $sections[$sid]['items'][] = [
                'id' => (int) $row['item_id'],
                'section_id' => $sid,
                'item_key' => (string) ($row['item_key'] ?? ''),
                'title' => (string) ($row['item_title'] ?? ''),
                'body' => (string) ($row['item_body'] ?? ''),
                'image_path' => (string) ($row['image_path'] ?? ''),
                'link_url' => (string) ($row['link_url'] ?? ''),
                'sort_order' => (int) $row['item_sort_order'],
            ];
        }
    }

    return array_values($sections);
}

function cms_get_home_slides(): array
{
    $cacheKey = cms_cache_key('home', 'slides');
    $cached = cache_get($cacheKey);
    if (is_array($cached)) {
        return $cached;
    }

    $fallback = [
        [
            'badge_text' => 'PRIVATE LABEL IS NOW SIMPLIFIED',
            'title' => 'Unleash your custom personal care line effortlessly We made it easier than you ever imagined',
            'description' => 'Launch Your Own Cosmetic Line & Amplify Your Brand With Our Expert Formulations, Empowering Your Success!',
            'button_text' => 'Explore Collection',
            'button_url' => 'shop.php',
            'image_path' => 'assets/imgs/hero/hero-img.png',
            'image_alt' => 'Beauty model',
        ],
        [
            'badge_text' => 'We Blend Science & Nature',
            'title' => 'Premium Ingredients with Effective Formulations',
            'description' => 'We offer 200+ products formulated with naturally derived and organic ingredients.',
            'button_text' => 'Try Our Products',
            'button_url' => 'about.php',
            'image_path' => 'assets/imgs/hero/hero_img2.png',
            'image_alt' => 'Private label product range',
        ],
        [
            'badge_text' => 'VITAMIN C FACIAL SERUM',
            'title' => 'Smoother & Brighter Skin',
            'description' => '',
            'button_text' => 'Learn More',
            'button_url' => 'contact.php',
            'image_path' => 'assets/imgs/hero/hero_img3.png',
            'image_alt' => 'Manufacturing and formulation',
        ],
    ];

    $pdo = db();
    if (!$pdo) {
        return $fallback;
    }

    $rows = db_fetch_all(
        $pdo,
        'SELECT id, badge_text, title, description, button_text, button_url, image_path, image_alt FROM home_slides WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
    );

    if (!$rows) {
        cache_set($cacheKey, $fallback, 300);
        return $fallback;
    }

    $slides = [];
    foreach ($rows as $row) {
        $slides[] = [
            'id' => (int) $row['id'],
            'badge_text' => (string) ($row['badge_text'] ?? ''),
            'title' => (string) ($row['title'] ?? ''),
            'description' => (string) ($row['description'] ?? ''),
            'button_text' => (string) ($row['button_text'] ?? ''),
            'button_url' => (string) ($row['button_url'] ?? ''),
            'image_path' => (string) ($row['image_path'] ?? ''),
            'image_alt' => (string) ($row['image_alt'] ?? ''),
        ];
    }

    cache_set($cacheKey, $slides, 300);
    return $slides;
}

function cms_invalidate_home_testimonials_cache(): void
{
    cache_delete(cms_cache_key('home', 'testimonials'));
}

function cms_invalidate_home_offices_cache(): void
{
    cache_delete(cms_cache_key('home', 'offices'));
}

function cms_invalidate_home_instagram_reels_cache(): void
{
    cache_delete(cms_cache_key('home', 'instagram_reels'));
}

function cms_get_home_testimonials(): array
{
    $cacheKey = cms_cache_key('home', 'testimonials');
    $cached = cache_get($cacheKey);
    if (is_array($cached)) {
        return $cached;
    }

    $fallback = [
        [
            'name' => 'Charlotte Evans',
            'location' => 'Birmingham, UK',
            'content' => 'I have sensitive skin, and most products irritate me. Your formulas changed that and gave visible results quickly.',
            'rating' => 5,
            'image_path' => 'assets/imgs/home/testimonial-thumb3_1.png',
        ],
        [
            'name' => 'Ava Thompson',
            'location' => 'Toronto, Canada',
            'content' => 'The private label journey was clear and professional. Their team guided us from samples to packaging and launch.',
            'rating' => 5,
            'image_path' => 'assets/imgs/home/testimonial-thumb3_1.png',
        ],
        [
            'name' => 'Liam Carter',
            'location' => 'Sydney, Australia',
            'content' => 'Strong product quality, practical MOQ support, and smooth communication at every stage.',
            'rating' => 5,
            'image_path' => 'assets/imgs/home/testimonial-thumb3_1.png',
        ],
    ];

    $pdo = db();
    if (!$pdo) {
        return $fallback;
    }

    $rows = db_fetch_all($pdo, 'SELECT id, name, location, content, rating, image_path FROM home_testimonials WHERE is_active = 1 ORDER BY sort_order ASC, id ASC');
    if (!$rows) {
        cache_set($cacheKey, $fallback, 300);
        return $fallback;
    }

    $out = [];
    foreach ($rows as $row) {
        $out[] = [
            'id' => (int) $row['id'],
            'name' => (string) ($row['name'] ?? ''),
            'location' => (string) ($row['location'] ?? ''),
            'content' => (string) ($row['content'] ?? ''),
            'rating' => max(1, min(5, (int) ($row['rating'] ?? 5))),
            'image_path' => (string) ($row['image_path'] ?? ''),
        ];
    }

    cache_set($cacheKey, $out, 300);
    return $out;
}

function cms_get_home_offices(): array
{
    $cacheKey = cms_cache_key('home', 'offices');
    $cached = cache_get($cacheKey);
    if (is_array($cached)) {
        return $cached;
    }

    $fallback = [
        [
            'country' => 'India',
            'address' => 'D226, 10th Avenue, Gaur City 2\nGr. Noida West - 201301, INDIA',
            'email' => 'info@mybrandplease.com',
            'phone' => '+91 (971) 700 4615',
            'image_path' => 'assets/imgs/home/office/INDIAN.webp',
        ],
        [
            'country' => 'United States',
            'address' => '59th Terrace, SW, West Park\nFlorida - 33023, UNITED STATES',
            'email' => 'info@mybrandplease.com',
            'phone' => '+1 (343) 322 5866',
            'image_path' => 'assets/imgs/home/office/USA-FLAG.webp',
        ],
        [
            'country' => 'Canada',
            'address' => 'K2C 3N8 ON, McWatters Road\nOntario, Ottawa, CANADA',
            'email' => 'barb@mybrandplease.com',
            'phone' => '+1 (819) 593 8620',
            'image_path' => 'assets/imgs/home/office/canada.webp',
        ],
        [
            'country' => 'Australia',
            'address' => '811 Pacific Highway, Chatswood\nSydney, AUSTRALIA',
            'email' => 'info@mybrandplease.com',
            'phone' => '+61 (422) 833 441',
            'image_path' => 'assets/imgs/home/office/Australia-Flag-1.webp',
        ],
    ];

    $pdo = db();
    if (!$pdo) {
        return $fallback;
    }

    $rows = db_fetch_all($pdo, 'SELECT id, country, address, email, phone, image_path FROM home_offices WHERE is_active = 1 ORDER BY sort_order ASC, id ASC');
    if (!$rows) {
        cache_set($cacheKey, $fallback, 300);
        return $fallback;
    }

    $out = [];
    foreach ($rows as $row) {
        $out[] = [
            'id' => (int) $row['id'],
            'country' => (string) ($row['country'] ?? ''),
            'address' => (string) ($row['address'] ?? ''),
            'email' => (string) ($row['email'] ?? ''),
            'phone' => (string) ($row['phone'] ?? ''),
            'image_path' => (string) ($row['image_path'] ?? ''),
        ];
    }

    cache_set($cacheKey, $out, 300);
    return $out;
}

function cms_get_home_instagram_reels(): array
{
    $cacheKey = cms_cache_key('home', 'instagram_reels');
    $cached = cache_get($cacheKey);
    if (is_array($cached)) {
        return $cached;
    }

    $fallback = [];

    $pdo = db();
    if (!$pdo) {
        return $fallback;
    }

    $rows = db_fetch_all(
        $pdo,
        'SELECT id, reel_url, video_path, sort_order, is_active FROM home_instagram_reels WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
    );

    if (!$rows) {
        cache_set($cacheKey, $fallback, 300);
        return $fallback;
    }

    $out = [];
    foreach ($rows as $row) {
        $out[] = [
            'id' => (int) ($row['id'] ?? 0),
            'reel_url' => (string) ($row['reel_url'] ?? ''),
            'video_path' => (string) ($row['video_path'] ?? ''),
            'sort_order' => (int) ($row['sort_order'] ?? 0),
            'is_active' => (int) ($row['is_active'] ?? 1),
        ];
    }

    cache_set($cacheKey, $out, 300);
    return $out;
}

function cms_get_why_choose_pages(bool $publishedOnly = true): array
{
    $pdo = db();
    if (!$pdo) {
        return [];
    }

    $sql = 'SELECT p.id, p.title, p.slug, p.status, pm.meta_title, pm.meta_description, pm.meta_keywords, pm.canonical_url
            FROM pages p
            LEFT JOIN page_meta pm ON pm.page_id = p.id
            WHERE p.page_group = :grp';
    $params = [':grp' => 'why_choose_us'];

    if ($publishedOnly) {
        $sql .= ' AND p.status = :st';
        $params[':st'] = 'published';
    }

    $sql .= ' ORDER BY p.updated_at DESC, p.id DESC';
    return db_fetch_all($pdo, $sql, $params);
}
