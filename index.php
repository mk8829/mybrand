<?php
require_once __DIR__ . '/includes/catalog.php';
require_once __DIR__ . '/includes/cms.php';

$homeProducts = array_slice(catalog_products(), 0, 8);
$homeCategories = catalog_categories();
$homeCategoryDisplay = [
  'skin-care' => [
    'label' => 'SKIN CARE',
    'children' => [
      'skin-care-environmental-defense',
      'skin-care-advanced',
      'skin-care-age-defying',
      'skin-care-peptides',
      'vitamin-c',
      'skin-care-brightening',
      'skin-care-super-fruits',
      'skin-care-marine-complex',
      'skin-care-blemish-prone-skin',
      'skin-care-botanical',
    ],
  ],
  'body-care' => ['label' => 'BODY CARE'],
  'hair-care' => ['label' => 'HAIR CARE'],
  'bathing-soaps' => ['label' => 'BATHING SOAPS'],
  'men-s-care' => ['label' => "FOR MEN'S"],
];
$homeCategoryBySlug = [];
foreach ($homeCategories as $category) {
  $homeCategoryBySlug[(string) $category['slug']] = $category;
}
$homeCategories = [];
foreach ($homeCategoryDisplay as $slug => $display) {
  if (!isset($homeCategoryBySlug[$slug])) {
    continue;
  }
  $category = $homeCategoryBySlug[$slug];
  $category['display_name'] = $display['label'];
  if (!empty($display['children']) && !empty($category['subcategories'])) {
    $childOrder = array_flip($display['children']);
    $children = array_values(array_filter($category['subcategories'], static function (array $child) use ($childOrder): bool {
      return isset($childOrder[(string) $child['slug']]);
    }));
    usort($children, static function (array $a, array $b) use ($childOrder): int {
      return ($childOrder[(string) $a['slug']] ?? PHP_INT_MAX) <=> ($childOrder[(string) $b['slug']] ?? PHP_INT_MAX);
    });
    $category['subcategories'] = $children;
  }
  $homeCategories[] = $category;
}
$homeSlides = cms_get_home_slides();
$homeTestimonials = cms_get_home_testimonials();
$homeOffices = cms_get_home_offices();
$homeInstagramReels = cms_get_home_instagram_reels();
$meta = [
  'title' => 'Mybrandplease | Home',
  'description' => 'Mybrandplease - Home page',
  'canonical' => 'index.php'
];
include 'includes/head.php';
include 'includes/header.php';
?>

<!-- Logout Success Message -->
<?php if (isset($_GET['logout']) && $_GET['logout'] === 'success' && isset($_SESSION['logout_message'])): ?>
<div class="logout-success-message" id="logout-message">
    <div class="logout-success-content">
        <div class="logout-success-icon">
            <i class="fa-solid fa-check-circle"></i>
        </div>
        <div class="logout-success-text">
            <h3>Successfully Logged Out</h3>
            <p><?php echo htmlspecialchars($_SESSION['logout_message'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <button class="logout-success-close" onclick="closeLogoutMessage()" aria-label="Close message">
            <i class="fa-solid fa-times"></i>
        </button>
    </div>
</div>
<?php unset($_SESSION['logout_message']); ?>
<?php endif; ?>

<script>
// Auto-hide logout message after 5 seconds
setTimeout(function() {
    const message = document.getElementById('logout-message');
    if (message) {
        message.classList.add('hide');
        setTimeout(function() {
            message.remove();
        }, 300);
    }
}, 5000);

// Manual close function
function closeLogoutMessage() {
    const message = document.getElementById('logout-message');
    if (message) {
        message.classList.add('hide');
        setTimeout(function() {
            message.remove();
        }, 300);
    }
}
</script>

<!-- Intro1 Section Start -->
<section class="intro1 rr-ov-hidden">
  <div class="intro1__bg-text">Mybrandplease</div>
  <div class="container">
    <div class="swiper intro1-slider">
      <div class="swiper-wrapper">
        <?php foreach ($homeSlides as $slide): ?>
          <div class="swiper-slide">
            <div class="row align-items-center">
              <div class="col-lg-5 col-md-12">
                <div class="intro1__content">
                  <?php if (!empty($slide['badge_text'])): ?>
                    <span class="intro1__content-subtext"><?php echo htmlspecialchars((string) $slide['badge_text'], ENT_QUOTES, 'UTF-8'); ?></span>
                  <?php endif; ?>
                  <h2 class="intro1__content-title"><?php echo htmlspecialchars((string) $slide['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                  <?php if (!empty($slide['description'])): ?>
                    <p class="intro1__content-desc mt-3"><?php echo htmlspecialchars((string) $slide['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                  <?php endif; ?>
                  <?php if (!empty($slide['button_text']) && !empty($slide['button_url'])): ?>
                    <div class="intro1__content-button">
                      <a href="<?php echo htmlspecialchars(url((string) $slide['button_url']), ENT_QUOTES, 'UTF-8'); ?>" class="rr-btn-button">
                        <span class="text"><?php echo htmlspecialchars((string) $slide['button_text'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="icon">
                          <svg width="16" height="10" viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.599976 4.59998H14.6M14.6 4.59998L10.6 8.59998M14.6 4.59998L10.6 0.599976" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                          </svg>
                        </span>
                      </a>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <div class="col-lg-7 col-md-7 col-sm-12">
                <div class="intro1__thumb">
                  <img src="<?php echo htmlspecialchars(url((string) $slide['image_path']), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) ($slide['image_alt'] ?: $slide['title']), ENT_QUOTES, 'UTF-8'); ?>" class="main-img">
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="intro1__shape1"><img src="assets/imgs/hero/hero-shape1_1.png" alt="shape"></div>
  <div class="intro1__shape2"><img src="assets/imgs/hero/hero-shape1_2.png" alt="shape"></div>
  <div class="intro1__shape3"><img src="assets/imgs/hero/hero-shape1_3.png" alt="shape"></div>
</section>
<!-- Intro1 Section End -->





        <section class="category1 section-spacing-120 rr-ov-hidden">
          <div class="category1-wrapper">
            <div class="container rr-container-1350">
              <!-- <div class="section-heading wow fadeInRight" data-wow-delay="0.3s">
                <h2 class="section-heading__title">OUR CATEGORY</h2>
              </div> -->
              <div class="row g-4">
                <div class="col-md-3 col-xl-3">
                  <div class="category1-item wow fadeInRight" data-wow-delay="0.2s">
                    <div class="category1-item__thumb">
                      <img src="assets/imgs/category/category_thumb1_2.jpeg" alt="thumb">
                    </div>
                    <div class="category1-item__content2">
                      </h2>
                      <div class="category1-item__button">
                        <a href="shop.php" class="rr-btn-button2">
                          <span class="text">Explore now</span>
                          <span class="icon">
                            <svg width="11" height="7" viewBox="0 0 11 7" fill="none"
                              xmlns="http://www.w3.org/2000/svg">
                              <path
                                d="M0.419556 3.21674H10.2097M10.2097 3.21674L7.41253 6.01393M10.2097 3.21674L7.41253 0.419556"
                                stroke="#0C0C0C" stroke-width="0.839157" stroke-linecap="round" stroke-linejoin="round">
                              </path>
                            </svg>
                          </span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-xl-6">
                  <div class="category1-item wow fadeInRight" data-wow-delay="0.3s">
                    <div class="category1-item__thumb">
                      <img src="assets/imgs/category/category_thumb1_1.jpeg" alt="thumb">
                    </div>
                    <div class="category1-item__content2">
                      </h2>
                      <div class="category1-item__button">
                        <a href="shop.php" class="rr-btn-button2">
                          <span class="text">Try Our Products</span>
                          <span class="icon">
                            <svg width="11" height="7" viewBox="0 0 11 7" fill="none"
                              xmlns="http://www.w3.org/2000/svg">
                              <path
                                d="M0.419556 3.21674H10.2097M10.2097 3.21674L7.41253 6.01393M10.2097 3.21674L7.41253 0.419556"
                                stroke="#0C0C0C" stroke-width="0.839157" stroke-linecap="round" stroke-linejoin="round">
                              </path>
                            </svg>
                          </span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-xl-3">
                  <div class="category1-item wow fadeInRight" data-wow-delay="0.5s">
                    <div class="category1-item__thumb">
                      <img src="assets/imgs/category/category_thumb1_3.jpeg" alt="thumb">
                    </div>
                    <div class="category1-item__content2">
                      </h2>
                      <div class="category1-item__button">
                        <a href="shop.php" class="rr-btn-button2">
                          <span class="text">Contact Us</span>
                          <span class="icon">
                            <svg width="11" height="7" viewBox="0 0 11 7" fill="none"
                              xmlns="http://www.w3.org/2000/svg">
                              <path
                                d="M0.419556 3.21674H10.2097M10.2097 3.21674L7.41253 6.01393M10.2097 3.21674L7.41253 0.419556"
                                stroke="#0C0C0C" stroke-width="0.839157" stroke-linecap="round" stroke-linejoin="round">
                              </path>
                            </svg>
                          </span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="intro1-slider__arrow intro1-slider__arrow--prev"><i class="fa-solid fa-angle-left"></i></div>
              <div class="intro1-slider__arrow intro1-slider__arrow--next"><i class="fa-solid fa-angle-right"></i></div>
              <div class="intro1-slider__dots"></div>
            </div>
          </div>
        </section>


        <section class="category-section section-spacing-120 rr-ov-hidden pt-0">
          <div class="container rr-container-1350">
            <div class="nav-tabs-modern" id="homeCategoryTabs">
              <?php foreach ($homeCategories as $idx => $cat): ?>
                <a href="#" class="nav-tab-item <?= $idx === 0 ? 'active' : '' ?>" data-cat="<?= htmlspecialchars((string)$cat['slug'], ENT_QUOTES, 'UTF-8') ?>">
                  <?= htmlspecialchars((string)($cat['display_name'] ?? $cat['name']), ENT_QUOTES, 'UTF-8') ?>
                </a>
              <?php endforeach; ?>
            </div>

            <div class="cat-grid" id="homeCategoryGrid"></div>
          </div>

          <script>
            (function(){
              const categories = <?= json_encode($homeCategories, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
              const tabsWrap = document.getElementById('homeCategoryTabs');
              const grid = document.getElementById('homeCategoryGrid');
              if (!tabsWrap || !grid || !Array.isArray(categories) || categories.length === 0) return;

              const esc = (v) => String(v ?? '').replace(/[&<>'"]/g, (m) => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[m]));
              const appBase = <?= json_encode(rtrim(dirname(url('shop.php')), '/\\'), JSON_UNESCAPED_SLASHES) ?>;
              const toUrl = (path) => {
                const raw = String(path || '').trim();
                if (raw === '') return '';
                if (/^(https?:)?\/\//i.test(raw)) return raw;
                const normalized = raw.replace(/^\/+/, '').replace(/^mybrand\//i, '');
                return appBase + '/' + normalized;
              };

              function renderCards(slug) {
                const active = categories.find((c) => c.slug === slug) || categories[0];
                const items = Array.isArray(active.subcategories) && active.subcategories.length
                  ? active.subcategories.map((s) => ({
                      name: s.name,
                      slug: s.slug,
                      image: s.image || active.image,
                      href: <?= json_encode(url('shop.php'), JSON_UNESCAPED_SLASHES) ?> + '?category=' + encodeURIComponent(active.slug) + '&subcategory=' + encodeURIComponent(s.slug)
                    }))
                  : [{
                      name: active.name,
                      slug: active.slug,
                      image: active.image,
                      href: <?= json_encode(url('shop.php'), JSON_UNESCAPED_SLASHES) ?> + '?category=' + encodeURIComponent(active.slug)
                    }];

                const cards = items.map((item) => `
                  <a href="${item.href}" class="cat-card">
                    <img src="${toUrl(item.image)}" class="cat-image" alt="${esc(item.name)}">
                    <div class="cat-overlay">
                      <h3 class="cat-title">${esc(item.name)}</h3>
                    </div>
                  </a>
                `).join('');
                grid.innerHTML =cards;
              }

              tabsWrap.addEventListener('click', function(e){
                const link = e.target.closest('.nav-tab-item');
                if (!link) return;
                e.preventDefault();
                tabsWrap.querySelectorAll('.nav-tab-item').forEach((el) => el.classList.remove('active'));
                link.classList.add('active');
                renderCards(link.getAttribute('data-cat'));
              });

              const first = tabsWrap.querySelector('.nav-tab-item.active') || tabsWrap.querySelector('.nav-tab-item');
              renderCards(first ? first.getAttribute('data-cat') : categories[0].slug);
            })();
          </script>
        </section>

        <section class="py-5 bg-light js-why-business">
          <div class="container">
            <div class="text-center mb-5">
              <h2 class="display-6 fw-bold mb-3" style="color: #2d3436;">Why Private Label Business?</h2>
              <p class="text-muted word-spacing-6 lh-base mx-auto fs-18" >
                Enhance your brand reputation and profitability by leveraging our specialised private label cosmetic products, with low minimums order quantity and competitive prices, our high-quality offerings foster customer loyalty, robust margins, and substantial returns. Maximize your brand's potential with our premium cosmetics solutions.
              </p>
            </div>

            <div class="row g-4">
              
              <div class="col-md-6 col-lg-3 ">
                <div class="card h-100 align-items-center border-0 shadow-sm overflow-hidden rounded-4 js-why-card">
                  <img src="assets/imgs/home/High-Profit-min-500x500.jpg" class="card-img-top img-80" alt="Profit growth">
                  <div class="card-body p-4 py-1 text-center">
                    <h5 class="fw-bold mb-2 theme-color-font">Higher Profits</h5>
                    <p class=" text-muted py-3 text-justify">Our high-quality natural and organic-based skin and hair care products are offered at costs comparable to or lower than leading brands, but you set the price.</p>
                  </div>
                  <div class="card-footer bg-white border-0 pb-4 text-center">
                    <span class="fs-15 theme-color-font rounded-pill bg-success-subark px-3">No More MSRP!</span>
                  </div>
                </div>
              </div>

              <div class="col-md-6 col-lg-3">
                <div class="card h-100 align-items-center border-0 shadow-sm overflow-hidden rounded-4 js-why-card">
                  <img src="assets/imgs/home/Increased-Sales-min-500x500.jpg" class="card-img-top img-80" alt="Increased sales">
                  <div class="card-body p-4 py-1 text-center">
                    <h5 class="fw-bold mb-2 theme-color-font">Increased Sales</h5>
                    <p class=" text-muted py-3 text-justify">Engaging your self in marketing your own brand where margin and product sale price in your absolute control where you take better marketing approach and decisions.</p>
                  </div>
                  <div class="card-footer bg-white border-0 pb-4 text-center">
                    <span class="fs-15 theme-color-font rounded-pill bg-primary-subark px-3">Manage with flexibility.</span>
                  </div>
                </div>
              </div>

              <div class="col-md-6 col-lg-3">
                <div class="card h-100 align-items-center border-0 shadow-sm overflow-hidden rounded-4 js-why-card">
                  <img src="assets/imgs/home/Client-Retension-min-500x500.jpg" class="card-img-top img-80" alt="Customer loyalty">
                  <div class="card-body p-4 py-1 text-center">
                    <h5 class="fw-bold mb-2 theme-color-font">Client Retention</h5>
                    <p class=" text-muted py-3 text-justify">Retain your customers with you with your brand. We are committed to offer you rock bottom price and yet the premium products experience. Create a BRAND LOYALTY.</p>
                  </div>
                  <div class="card-footer bg-white border-0 pb-4 text-center">
                    <span class="fs-15 theme-color-font rounded-pill bg-warning-subark px-3">Your Success Is Our Success.</span>
                  </div>
                </div>
              </div>

              <div class="col-md-6 col-lg-3">
                <div class="card h-100 align-items-center border-0 shadow-sm overflow-hidden rounded-4 js-why-card">
                  <img src="assets/imgs/home/Brand-Equity-min-500x500.jpg" class="card-img-top img-80" alt="Brand equity">
                  <div class="card-body p-4 py-1 text-center">
                    <h5 class="fw-bold mb-2 theme-color-font">Brand Equity</h5>
                    <p class=" text-muted py-3 text-justify">Building sales of your own brand of skin and hair care products not only builds your prestige in the mind of your customers but also in the market and leads to BRAND LOYALTY.</p>
                  </div>
                  <div class="card-footer bg-white border-0 pb-4 text-center">
                    <span class="fs-15 theme-color-font rounded-pill bg-info-subtle px-3">Give Your Work Deeper Meaning.</span>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </section>

        <section class="offer1 section-spacing-120  rr-ov-hidden pb-0">
          <div class="container rr-container-1350">
            <div class="offer1-wrapper background-image wow fadeInUp"
              style="background-image: url(assets/imgs/offer/offer-banner.jpeg);" data-wow-delay=".3s">
              <div class="row">
                <div class="col-xl-12 d-flex justify-content-end">
                  <div class="offer1__content">
                    <!-- <span class="offer1__content-text">A nature`s touch</span> -->
                    <h2 class="offer1__content-title"><span class="subtitle">Get 15%</span> Off All Private Label Work </h2>
                    <p class="offer1__content-subtext">Unlock quantity discounts for your private label work and maximize your savings with us. </p>
                    <div class="offer1__content-button">
                      <a href="shop.php" class="rr-btn-button2">
                        <span class="text">Browse product</span>
                        <span class="icon">
                          <svg width="11" height="7" viewBox="0 0 11 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                              d="M0.419556 3.21674H10.2097M10.2097 3.21674L7.41253 6.01393M10.2097 3.21674L7.41253 0.419556"
                              stroke="#0C0C0C" stroke-width="0.839157" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                          </svg>
                        </span>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section class="section-spacing-120 rr-ov-hidden">
          <div class="container-fluid p-0">
            <div class="section-heading text-center wow fadeInUp" data-wow-delay=".2s">
              <span class="section-heading__subtitle">How We Build Your Brand</span>
              <h2 class="section-heading__title">From sample selection to finished private label product.</h2>
            </div>

            <img src="assets/imgs/home/sample-selection.jpeg" alt="">
            <div class="text-center mt-3 wow fadeInUp" data-wow-delay=".5s">
                <a href="how-it-works.php" class="rr-btn-button">
                  <span class="text">Explore More</span>
                  <span class="icon">
                    <svg width="16" height="10" viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M0.6 4.6H14.6M14.6 4.6L10.6 8.6M14.6 4.6L10.6 0.6" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                  </span>
                </a>
              </div>
          </div>
        </section>

        <!-- Milestone Section Start -->
        <section class="milestone-highlight section-spacing-120 rr-ov-hidden">
          <div class="milestone-highlight__overlay"></div>
          <div class="container">
            <div class="section-heading wow fadeInUp" data-wow-delay=".3s">
              <h2 class="section-heading__title">~Our Milestones~</h2>
            </div>
            <div class="milestone-grid">
              <div class="milestone-card wow fadeInUp" data-wow-delay=".1s">
                <div class="milestone-card__icon-wrap">
                  <img src="assets/imgs/home/milestone/4381dcfc16-300x254.webp" alt="Monthly worldwide inquiries">
                </div>
                <h3 class="milestone-card__number js-milestone-number" data-target="1075">0+</h3>
                <p class="milestone-card__text">Monthly Worldwide Inquires</p>
              </div>
              <div class="milestone-card wow fadeInUp" data-wow-delay=".2s">
                <div class="milestone-card__icon-wrap">
                  <img src="assets/imgs/home/milestone/f99c232e29-2-300x202.webp" alt="Customers served monthly">
                </div>
                <h3 class="milestone-card__number js-milestone-number" data-target="950">0+</h3>
                <p class="milestone-card__text">Customer's Served Monthly</p>
              </div>
              <div class="milestone-card wow fadeInUp" data-wow-delay=".3s">
                <div class="milestone-card__icon-wrap">
                  <img src="assets/imgs/home/milestone/ec2ce0607f-150x150.webp" alt="Contract manufacturing for brands">
                </div>
                <h3 class="milestone-card__number js-milestone-number" data-target="650">0+</h3>
                <p class="milestone-card__text">Contract Manufacturing for Brands</p>
              </div>
              <div class="milestone-card wow fadeInUp" data-wow-delay=".4s">
                <div class="milestone-card__icon-wrap">
                  <img src="assets/imgs/home/milestone/b3099fe017-150x150.webp" alt="Ayurvedic personal care formulations">
                </div>
                <h3 class="milestone-card__number js-milestone-number" data-target="525">0+</h3>
                <p class="milestone-card__text">Ayurvedic Personal Care Formulations</p>
              </div>
            </div>
          </div>
        </section>
        <!-- Milestone Section End -->

        <!-- Global Presence Map Section Start -->
        <section class="global-presence section-spacing-120 rr-ov-hidden js-global-presence">
          <div class="container-fluid">
            <div class="section-heading wow fadeInUp" data-wow-delay=".2s">
              <h2 class="section-heading__title">~ Our Global Presence ~</h2>
            </div>
            <div class="global-map-stage wow fadeInUp" data-wow-delay=".3s">
              <img class="global-map-image" src="assets/imgs/home/map2.webp" alt="World map">

              <button class="map-marker map-marker--north-america" type="button" aria-label="North America">
                <span class="map-marker__dot"></span>
                <span class="map-marker__popup">
                  <img src="assets/imgs/home/office/USA-FLAG.webp" alt="North America">
                  <strong>North America</strong>
                </span>
              </button>
              <button class="map-marker map-marker--united-kingdom" type="button" aria-label="United Kingdom">
                <span class="map-marker__dot"></span>
                <span class="map-marker__popup">
                  <img src="assets/imgs/home/office/Flag-United-Kingdom.webp" alt="United Kingdom">
                  <strong>United Kingdom</strong>
                </span>
              </button>
              <button class="map-marker map-marker--canada" type="button" aria-label="Canada">
                <span class="map-marker__dot"></span>
                <span class="map-marker__popup">
                  <img src="assets/imgs/home/office/canada.webp" alt="Canada flag">
                  <strong>Canada</strong>
                </span>
              </button>
              <button class="map-marker map-marker--africa" type="button" aria-label="Africa">
                <span class="map-marker__dot"></span>
                <span class="map-marker__popup">
                  <img src="assets/imgs/home/office/africa.png" alt="Africa flag">
                  <strong>Africa</strong>
                </span>
              </button>
              <button class="map-marker map-marker--europe" type="button" aria-label="Europe">
                <span class="map-marker__dot"></span>
                <span class="map-marker__popup">
                  <img src="assets/imgs/home/office/europe.png" alt="Europe flag">
                  <strong>Europe</strong>
                </span>
              </button>
              <button class="map-marker map-marker--asia" type="button" aria-label="Asia">
                <span class="map-marker__dot"></span>
                <span class="map-marker__popup">
                  <img src="assets/imgs/home/office/INDIAN.webp" alt="Asia flag">
                  <strong>Asia</strong>
                </span>
              </button>
              <button class="map-marker map-marker--australia" type="button" aria-label="Australia">
                <span class="map-marker__dot"></span>
                <span class="map-marker__popup">
                  <img src="assets/imgs/home/office/Australia-Flag-1.webp" alt="Australia flag">
                  <strong>Australia</strong>
                </span>
              </button>
              <button class="map-marker map-marker--south-america" type="button" aria-label="South America">
                <span class="map-marker__dot"></span>
                <span class="map-marker__popup">
                  <img src="assets/imgs/home/office/USA-FLAG.webp" alt="South America">
                  <strong>South America</strong>
                </span>
              </button>
            </div>
          </div>
        </section>
        <!-- Global Presence Map Section End -->

        <section class="testimonial3 section-spacing-120 bg-light-pick rr-ov-hidden">
          <div class="container rr-container-1350">
            <div class="section-heading wow fadeInRight" data-wow-delay="0.3s">
              <span class="section-heading__subtitle wow fadeInRight" data-wow-delay="0.3s">testimonial</span>
              <h2 class="section-heading__title">WHAT OUR CLIENTS SAY</h2>
            </div>
            <div class="testimonial3-wrapper">
              <div class="row">
                <div class="col-lg-4">
                  <div class="testimonial3-items">
                    <div class="testimonial3-items__circle"></div>
                    <div class="testimonial3-items__thumb">
                      <img src="assets/imgs/hero/hero-rating-user3_1.png" alt="testimonial">
                    </div>
                    <div class="testimonial3-items__icon">
                      <i class="fa-solid fa-quote-right"></i>
                    </div>
                  </div>
                </div>
                <div class="col-lg-8">
                  <div class="swiper testimonial3-slider">
                    <div class="swiper-wrapper">
                      <?php foreach ($homeTestimonials as $t): ?>
                        <div class="swiper-slide" data-thumb="<?php echo htmlspecialchars(url((string) ($t['image_path'] ?? 'assets/imgs/home/testimonial-thumb3_1.png')), ENT_QUOTES, 'UTF-8'); ?>">
                          <div class="testimonial3-card">
                            <div class="testimonial3-card__content">
                              <div class="testimonial3-card__content-star">
                                <?php for ($i = 0; $i < (int) ($t['rating'] ?? 5); $i++): ?>
                                  <i class="fa-solid fa-star"></i>
                                <?php endfor; ?>
                              </div>
                              <div class="testimonial3-card__content-title">"<?php echo htmlspecialchars((string) ($t['content'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"</div>
                              <div class="testimonial3-card__content-subtitle"><?php echo htmlspecialchars((string) ($t['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                              <p class="testimonial3-card__content-text"><?php echo htmlspecialchars((string) ($t['location'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                  <div class="testimonial3-controls">
                    <div class="testimonial3-controls__arrowLeft"><i class="fa-solid fa-angle-left"></i>Previous</div>
                    <div class="testimonial3-controls__arrowRight">next <i class="fa-solid fa-angle-right"></i></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section class="social-reels bg-light-pick rr-ov-hidden">
          <div class="social-reels__track" aria-label="Customer social reels">
            <?php foreach ($homeInstagramReels as $idx => $reel): ?>
              <?php
                $reelUrl = (string) ($reel['reel_url'] ?? '');
                $videoPath = (string) ($reel['video_path'] ?? '');
                $cleanUrl = preg_replace('/\?.*$/', '', $reelUrl) ?: $reelUrl;
                $cleanUrl = rtrim($cleanUrl, '/');
                $embedUrl = str_ends_with($cleanUrl, '/embed') ? $cleanUrl : ($cleanUrl !== '' ? ($cleanUrl . '/embed') : '');
                if ($videoPath === '' && $embedUrl === '') {
                  continue;
                }
              ?>
              <div
                class="social-reels__card js-reel-card"
                aria-label="Open reel <?php echo $idx + 1; ?>"
                <?php if ($videoPath !== ''): ?>
                  data-video-src="<?php echo htmlspecialchars(url($videoPath), ENT_QUOTES, 'UTF-8'); ?>"
                <?php endif; ?>>
                <?php if ($videoPath !== ''): ?>
                  <video
                    src="<?php echo htmlspecialchars(url($videoPath), ENT_QUOTES, 'UTF-8'); ?>"
                    class="social-reels__video"
                    autoplay
                    muted
                    loop
                    playsinline
                    preload="metadata"></video>
                  <button class="social-reels__volume-btn" type="button" aria-label="Unmute reel" aria-pressed="false">
                    <i class="fa-solid fa-volume-xmark" aria-hidden="true"></i>
                  </button>
                <?php elseif ($embedUrl !== ''): ?>
                  <iframe
                    src="<?php echo htmlspecialchars($embedUrl, ENT_QUOTES, 'UTF-8'); ?>"
                    class="social-reels__iframe"
                    title="Instagram reel <?php echo $idx + 1; ?>"
                    loading="lazy"
                    allow="autoplay; encrypted-media; picture-in-picture; clipboard-write"
                    allowfullscreen>
                  </iframe>
                <?php endif; ?>
                <?php if ($reelUrl !== ''): ?>
                  <a href="<?php echo htmlspecialchars((string) $reelUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="social-reels__badge social-reels__badge--link" aria-label="Open Instagram reel <?php echo $idx + 1; ?>">
                    <i class="fa-brands fa-instagram"></i>
                  </a>
                <?php else: ?>
                  <span class="social-reels__badge"><i class="fa-brands fa-instagram"></i></span>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <!-- Office Section Start -->
        <section class="office-showcase section-spacing-120 rr-ov-hidden">
          <div class="container">
            <div class="section-heading wow fadeInUp" data-wow-delay=".3s">
              <h2 class="section-heading__title">~ Our Offices ~</h2>
            </div>
            <div class="office-grid">
              <?php $officeDelay = 0.1; foreach ($homeOffices as $office): ?>
                <div class="office-card wow fadeInUp" data-wow-delay=".<?php echo (int) round($officeDelay * 10); ?>s">
                  <div class="office-card__flag">
                    <img src="<?php echo htmlspecialchars(url((string) ($office['image_path'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) ($office['country'] ?? 'Office'), ENT_QUOTES, 'UTF-8'); ?> Office">
                  </div>
                  <div class="office-card__pin"><i class="fa-solid fa-location-dot"></i></div>
                  <h3 class="office-card__title"><?php echo htmlspecialchars((string) ($office['country'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></h3>
                  <p class="office-card__address"><?php echo nl2br(htmlspecialchars((string) ($office['address'] ?? ''), ENT_QUOTES, 'UTF-8')); ?></p>
                  <?php if (!empty($office['email'])): ?><p class="office-card__meta"><strong>Email:</strong> <?php echo htmlspecialchars((string) $office['email'], ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
                  <?php if (!empty($office['phone'])): ?><p class="office-card__meta"><strong>Call / WhatsApp:</strong> <?php echo htmlspecialchars((string) $office['phone'], ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
                </div>
              <?php $officeDelay += 0.1; endforeach; ?>
            </div>
          </div>
        </section>
        <!-- Office Section End -->

        <!-- CTA Section Start -->
        <section class="cta-section section-spacing-120 rr-ov-hidden">
          <div class="container rr-container-1350">
            <div class="cta-wrapper">
              <div class="row align-items-center bg-white rounded-4 text-center">
                
                <div class="col-lg-6 ">
                  <div class="cta-form">
                    <div class="">
                      <h3 class="cta-form-title">Request a Free Consultation</h3>
                      <p class="cta-form-subtitle">Fill out the form below and our team will get back to you within 24 hours.</p>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                  <button class="cta-enquiry-btn" id="open-enquiry-btn" type="button">
                    <span class="cta-btn-text">Get Free Enquiry</span>
                    <span class="cta-btn-icon">
                      <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 10H15M15 10L10 5M15 10L10 15" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
        <!-- CTA Section End -->

        <!-- Auto-scroll Section Start -->
        <section class="auto-scroll-section section-spacing-120 rr-ov-hidden">
          <div class="container">
            <div class="section-heading wow fadeInUp" data-wow-delay=".3s">
              <h2 class="section-heading__title">OUR PARTNERS</h2>
            </div>
            <div class="auto-scroll-wrapper">
              <div class="auto-scroll-content">
                <div class="auto-scroll-item">
                  <img src="assets/imgs/home/Amazon-logo-min-300x126.jpg" alt="Amazon">
                </div>
                <div class="auto-scroll-item">
                  <img src="assets/imgs/home/Costco_Wholesale_logo-min-300x108.jpg" alt="Costco">
                </div>
                <div class="auto-scroll-item">
                  <img src="assets/imgs/home/desertcart-logo-min-300x74.jpg" alt="Desert Cart">
                </div>
                <div class="auto-scroll-item">
                  <img src="assets/imgs/home/EBay_logo-min-300x120.jpg" alt="eBay">
                </div>
                <div class="auto-scroll-item">
                  <img src="assets/imgs/home/Etsy-min-300x171.jpg" alt="Etsy">
                </div>
                <div class="auto-scroll-item">
                  <img src="assets/imgs/home/final_logo_1_37ee31bf-a041-4af1-9b0e-d86fd4b2da83-300x85.jpg" alt="MyBrand">
                </div>
                <div class="auto-scroll-item">
                  <img src="assets/imgs/home/iherb-min-300x117.jpg" alt="iHerb">
                </div>
                <div class="auto-scroll-item">
                  <img src="assets/imgs/home/Macys_Logo-min-300x86.jpg" alt="Macy's">
                </div>
                <div class="auto-scroll-item">
                  <img src="assets/imgs/home/Nordstrom-logo-min-300x169.jpg" alt="Nordstrom">
                </div>
                <div class="auto-scroll-item">
                  <img src="assets/imgs/home/Saks_Fifth_Avenue_Logo_-min-300x225.jpg" alt="Saks Fifth Avenue">
                </div>
                <div class="auto-scroll-item">
                  <img src="assets/imgs/home/target-min-300x83.jpg" alt="Target">
                </div>
                <div class="auto-scroll-item">
                  <img src="assets/imgs/home/the-detox-market-min-300x28.jpg" alt="The Detox Market">
                </div>
                <div class="auto-scroll-item">
                  <img src="assets/imgs/home/TJ_Maxx-min-300x96.jpg" alt="TJ Maxx">
                </div>
                <div class="auto-scroll-item">
                  <img src="assets/imgs/home/Walmart_logo.svg-min-300x72.jpg" alt="Walmart">
                </div>
              </div>
            </div>
          </div>
        </section>
        <!-- Auto-scroll Section End -->

<script>
  window.addEventListener('load', function () {
    const root = document;

    const imageTargets = root.querySelectorAll(
      '.intro1__thumb img, .category1-item__thumb img, .cat-image, .card-img-top, .milestone-card__icon-wrap img, .office-card__flag img, .auto-scroll-item img, .social-reels__video'
    );
      imageTargets.forEach(function (el) {
        if (!el.hasAttribute('data-aos')) el.setAttribute('data-aos', 'zoom-in');
        if (!el.hasAttribute('data-aos-duration')) el.setAttribute('data-aos-duration', '800');
      });

    const textTargets = root.querySelectorAll(
      '.section-heading__title, .section-heading__subtitle, .intro1__content-title, .intro1__content-desc, .cat-title, .milestone-card__text, .office-card__title, .office-card__address, .cta-form-title, .cta-form-subtitle'
    );
    textTargets.forEach(function (el, i) {
      if (!el.hasAttribute('data-aos')) el.setAttribute('data-aos', i % 2 === 0 ? 'fade-up' : 'fade-left');
      if (!el.hasAttribute('data-aos-duration')) el.setAttribute('data-aos-duration', '700');
    });

    const cardTargets = root.querySelectorAll(
      '.js-why-card, .milestone-card, .office-card, .cat-card, .social-reels__card'
    );
    cardTargets.forEach(function (el, i) {
      if (!el.hasAttribute('data-aos')) el.setAttribute('data-aos', 'fade-up');
      if (!el.hasAttribute('data-aos-delay')) el.setAttribute('data-aos-delay', String((i % 6) * 80));
      if (!el.hasAttribute('data-aos-duration')) el.setAttribute('data-aos-duration', '850');
    });

    if (window.AOS && typeof window.AOS.init === 'function') {
      window.AOS.init({
        once: false,
        mirror: true,
        offset: 60,
        duration: 800,
        easing: 'ease-out-cubic'
      });
    }

      if (window.gsap && window.ScrollTrigger) {
        window.gsap.registerPlugin(window.ScrollTrigger);

        const parallaxImages = root.querySelectorAll(
          '.intro1__thumb img, .category1-item__thumb img, .card-img-top, .office-card__flag img'
        );
        window.gsap.set(parallaxImages, { willChange: 'transform' });
        parallaxImages.forEach(function (img) {
          window.gsap.fromTo(img, { y: -12 }, {
            y: 12,
            ease: 'none',
            scrollTrigger: {
              trigger: img.closest('section') || img,
              start: 'top bottom',
              end: 'bottom top',
              scrub: true
            }
          });
        });
      }

      (function initGlobalMapSequence() {
        const stage = root.querySelector('.global-map-stage');
        if (!stage || window.innerWidth <= 991) return;

        const allMarkers = Array.from(stage.querySelectorAll('.map-marker'));
        if (!allMarkers.length) return;

        const zigzagOrder = [
          '.map-marker--canada',
          '.map-marker--north-america',
          '.map-marker--south-america',
          '.map-marker--united-kingdom',
          '.map-marker--europe',
          '.map-marker--africa',
          '.map-marker--asia',
          '.map-marker--australia'
        ];

        const ordered = [];
        zigzagOrder.forEach(function (sel) {
          const marker = stage.querySelector(sel);
          if (marker) ordered.push(marker);
        });
        allMarkers.forEach(function (m) {
          if (ordered.indexOf(m) === -1) ordered.push(m);
        });

        let cycleTimer = null;
        const stepDelay = 260;
        const cycleDelay = 5000;

        function clearVisible() {
          ordered.forEach(function (marker) {
            marker.classList.remove('is-visible');
          });
        }

        function runSequence() {
          clearVisible();

          ordered.forEach(function (marker, index) {
            setTimeout(function () {
              marker.classList.add('is-visible');
            }, index * stepDelay);
          });
        }

        function restartAuto() {
          if (cycleTimer) clearInterval(cycleTimer);
          cycleTimer = setInterval(runSequence, cycleDelay);
        }

        stage.addEventListener('mouseenter', function () {
          runSequence();
          restartAuto();
        });

        runSequence();
        restartAuto();
      })();
    });
  </script>

<?php include 'includes/footer.php'; ?>
