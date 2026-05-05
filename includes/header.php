<?php
require_once __DIR__ . '/url.php';
require_once __DIR__ . '/catalog.php';
require_once __DIR__ . '/cms.php';
require_once __DIR__ . '/user.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$headerCategories = catalog_categories();
$whyChoosePages = cms_get_why_choose_pages(true);
$headerLogo = url('assets/imgs/logo/mybrandplease.gif');
$headerCartCount = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $headerCartCount += max(1, (int) $qty);
    }
}
// Also try to load from database if session is empty
if ($headerCartCount === 0 && session_id()) {
    $pdo = db();
    if ($pdo) {
        $sessionId = session_id();
        $total = $pdo->prepare('SELECT SUM(quantity) as total FROM cart WHERE session_id = ?');
        $total->execute([$sessionId]);
        $result = $total->fetch();
        if ($result && !empty($result['total'])) {
            $headerCartCount = (int) $result['total'];
        }
    }
}

if (!function_exists('render_header_menu_items')) {
    function render_header_menu_items(array $items): void
    {
        foreach ($items as $item) {
            $hasChildren = !empty($item['children']);
            echo '<li' . ($hasChildren ? ' class="menu-item-has-children"' : '') . '>';
            echo '<a href="' . htmlspecialchars(url((string) $item['url']), ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars((string) $item['title'], ENT_QUOTES, 'UTF-8') . '</a>';
            if ($hasChildren) {
                echo '<ul class="dp-menu">';
                render_header_menu_items($item['children']);
                echo '</ul>';
            }
            echo '</li>';
        }
    }
}
?>


  <div class="progress-wrap">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
      <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"></path>
    </svg>
  </div>
  <div id="smooth-wrapper">
    <div id="smooth-content">
      <!-- Search Area Start (same as header-area-2) -->
      <!-- Side Panel Start -->
      <aside class="fix">
        <div class="side-info">
          <div class="side-info-content">
            <div class="offset-widget offset-header">
              <div class="offset-logo">
                <a href="<?php echo url('index.php'); ?>">
                  <img src="<?php echo $headerLogo; ?>" alt="MyBrandPlease Logo" />
                </a>
              </div>
              <button id="side-info-close" class="side-info-close">
                <i class="fas fa-times"></i>
              </button>
            </div>
            <div class="moc-offcanvas d-xl-none fix" id="mocOffcanvas">
              <div class="moc-panel" role="dialog" aria-modal="true" aria-labelledby="mocTitle" aria-hidden="true">
                <div class="moc-topbar">
                  <button type="button" class="moc-back" id="mocBack" aria-label="Back to main menu">
                    <span class="moc-back__icon" aria-hidden="true">&#8592;</span>
                    <span class="moc-back__label">Back</span>
                  </button>
                  <div class="moc-title" id="mocTitle">Menu</div>
                  <button type="button" class="moc-close side-info-close" aria-label="Close menu">&times;</button>
                </div>

                <div class="moc-track-wrap">
                  <div
                    class="moc-track"
                    id="mocTrack"
                    data-categories="<?php
                    $mobileCategories = array_map(static function ($cat) {
                      return [
                        'name' => (string) ($cat['name'] ?? ''),
                        'url' => catalog_shop_link((string) ($cat['slug'] ?? '')),
                      ];
                    }, $headerCategories);
                    echo htmlspecialchars(json_encode($mobileCategories), ENT_QUOTES, 'UTF-8');
                    ?>"
                    data-why-pages="<?php
                    $mobileWhyPages = array_map(static function ($page) {
                      return [
                        'title' => (string) ($page['title'] ?? ''),
                        'url' => why_page_url((string) ($page['slug'] ?? '')),
                      ];
                    }, $whyChoosePages);
                    echo htmlspecialchars(json_encode($mobileWhyPages), ENT_QUOTES, 'UTF-8');
                    ?>"
                  >
                    <nav class="moc-menu-panel moc-menu-panel--root" aria-label="Mobile root menu">
                      <ul class="moc-list">
                        <li><a class="moc-link" href="<?php echo url('index.php'); ?>">Home</a></li>
                        <li>
                          <button type="button" class="moc-link moc-parent" data-parent-label="About Us" data-parent-url="<?php echo url('about.php'); ?>" data-menu-key="about">
                            <span>About Us</span>
                            <span class="moc-chevron" aria-hidden="true">&#8250;</span>
                          </button>
                        </li>
                        <li>
                          <button type="button" class="moc-link moc-parent" data-parent-label="How it Works" data-parent-url="<?php echo url('how-it-works.php'); ?>" data-menu-key="works">
                            <span>How it Works</span>
                            <span class="moc-chevron" aria-hidden="true">&#8250;</span>
                          </button>
                        </li>
                        <li>
                          <button type="button" class="moc-link moc-parent" data-parent-label="Our Product" data-parent-url="<?php echo url('shop.php'); ?>" data-menu-key="products">
                            <span>Our Product</span>
                            <span class="moc-chevron" aria-hidden="true">&#8250;</span>
                          </button>
                        </li>
                        <li>
                          <button type="button" class="moc-link moc-parent" data-parent-label="Why Choose Us" data-parent-url="<?php echo url('our-services.php'); ?>" data-menu-key="why">
                            <span>Why Choose Us</span>
                            <span class="moc-chevron" aria-hidden="true">&#8250;</span>
                          </button>
                        </li>
                        <li>
                          <button type="button" class="moc-link moc-parent" data-parent-label="Resources" data-parent-url="<?php echo url('blog.php'); ?>" data-menu-key="resources">
                            <span>Resources</span>
                            <span class="moc-chevron" aria-hidden="true">&#8250;</span>
                          </button>
                        </li>
                        <li class="moc-cta-item">
                          <a class="moc-cta-btn" href="<?php echo url('meeting-schedule.php'); ?>">Get In Touch</a>
                        </li>
                      </ul>
                    </nav>

                    <nav class="moc-menu-panel moc-menu-panel--sub" aria-label="Mobile submenu">
                      <div class="moc-subhead">Sub Menu</div>
                      <ul class="moc-list" id="mocSubmenuList"></ul>
                    </nav>
                  </div>
                </div>
              </div>
            </div>
            <div class="offset-widget-box">
              <h2 class="title">Social Info</h2>
              <div class="offset-social">
                <a href="<?php echo url('contact.php'); ?>" class="facebook" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="<?php echo url('contact.php'); ?>" class="twitter" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="<?php echo url('contact.php'); ?>" class="linkedin" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                <a href="<?php echo url('contact.php'); ?>" class="youtube" aria-label="Youtube"><i class="fab fa-youtube"></i></a>
              </div>
            </div>
          </div>
        </div>
      </aside>
      <div class="offcanvas-overlay"></div>

      <!-- Header start -->
      <header class="header-area header-layoutone header-sticky">
        <div class="header-main">
          <div class="container">
            <div class="row align-items-center justify-content-between">
              <!-- Logo Column -->
              <div class="col-auto">
                <div class="header__logo">
                  <a href="<?php echo url('index.php'); ?>">
                    <img src="<?php echo $headerLogo; ?>" class="normal-logo" alt="MyBrandPlease Logo" />
                  </a>
                </div>
              </div>

              <!-- Navigation Column -->
              <div class="col d-none d-xl-flex justify-content-center">
                <nav class="main-menu">
                  <ul>
                    <li>
                      <a href="<?php echo url('index.php'); ?>">Home</a>
                    </li>
                    <li class="menu-item-has-children">
                      <a href="<?php echo url('about.php'); ?>">About Us</a>
                      <ul class="dp-menu">
                        <li><a href="<?php echo url('about.php'); ?>#who-we-are">Who We Are</a></li>
                        <li><a href="<?php echo url('about.php'); ?>#what-we-offer">What We Offer</a></li>
                        <li><a href="<?php echo url('about.php'); ?>#how-we-formulate">How We Formulate</a></li>
                        <li><a href="<?php echo url('about.php'); ?>#key-benifits">Key Benefits</a></li>
                        <li><a href="<?php echo url('about.php'); ?>">Our Certificates</a></li>
                      </ul>
                    </li>
                    <li class="menu-item-has-children">
                      <a href="<?php echo url('how-it-works.php'); ?>">How it Works</a>
                      <ul class="dp-menu">
                        <li><a href="<?php echo url('how-it-works.php'); ?>#product-components">Product Components</a></li>
                        <li><a href="<?php echo url('how-it-works.php'); ?>#define-offerings">Define Offerings</a></li>
                        <li><a href="<?php echo url('how-it-works.php'); ?>#design-and-printing">Design & Printing</a></li>
                        <li><a href="<?php echo url('how-it-works.php'); ?>#finishing-touches">Finishing Touches</a></li>
                      </ul>
                    </li>
                    <li class="menu-item-has-children">
                      <a href="<?php echo url('shop.php'); ?>">Our Product</a>
                      <ul class="dp-menu">
                        <li><a href="<?php echo url('shop.php'); ?>">All Categories</a></li>
                        <?php foreach ($headerCategories as $cat): ?>
                          <li class="<?php echo !empty($cat['subcategories']) ? '' : ''; ?>">
                            <a href="<?php echo htmlspecialchars(catalog_shop_link($cat['slug']), ENT_QUOTES, 'UTF-8'); ?>">
                              <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                            <?php if (!empty($cat['subcategories'])): ?>
                              <ul class="dp-menu">
                                <?php foreach ($cat['subcategories'] as $sub): ?>
                                  <li>
                                    <a href="<?php echo htmlspecialchars(catalog_shop_link($cat['slug'], $sub['slug']), ENT_QUOTES, 'UTF-8'); ?>">
                                      <?php echo htmlspecialchars($sub['name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                  </li>
                                <?php endforeach; ?>
                              </ul>
                            <?php endif; ?>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    </li>
                    <li class="menu-item-has-children">
                      <a href="<?php echo url('our-services.php'); ?>">Why Choose Us</a>
                      <ul class="dp-menu">
                        <?php foreach ($whyChoosePages as $whyPage): ?>
                          <li>
                            <a href="<?php echo htmlspecialchars(why_page_url((string) $whyPage['slug']), ENT_QUOTES, 'UTF-8'); ?>">
                              <?php echo htmlspecialchars((string) $whyPage['title'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                          </li>
                        <?php endforeach; ?>
                        <?php if (!$whyChoosePages): ?>
                          <li><a href="<?php echo why_page_url('private-label-skin-care-manufacturer'); ?>">Private Label Skin Care Manufacturer</a></li>
                        <?php endif; ?>
                      </ul>
                    </li>
                    
                    <li class="menu-item-has-children">
                      <a href="<?php echo url('blog.php'); ?>">Resources</a>
                      <ul class="dp-menu">
                        <li><a href="<?php echo url('blog.php'); ?>">Blog</a></li>
                        <li><a href="<?php echo url('faq.php'); ?>">FAQ's</a></li>
                        <li><a href="<?php echo url('contact.php'); ?>">Contact</a></li>
                        <li><a href="<?php echo url('form-center.php'); ?>">Form Center</a></li>
                        <li><a href="<?php echo url('product-catalog.php'); ?>">Product Catalog</a></li>
                        <li><a href="<?php echo url('data-sheets.php'); ?>">Material Safety Data Sheets</a></li>
                      </ul>
                    </li>
                    
                  </ul>
                </nav>
              </div>

              <!-- Actions Column -->
              <div class="col-auto">
                <div class="header-right d-flex align-items-center gap-3">
                  <a href="<?php echo url('meeting-schedule.php'); ?>" class="btn-orange d-inline-flex header-get-touch-btn" style="padding:10px 18px;font-size:14px;line-height:1.1;">
                    Get In Touch
                  </a>

                  <div class="header-lang d-none d-lg-flex">
                    <div class="header-lang-switcher" id="header-language-wrap" aria-expanded="false">
                      <input type="hidden" id="header-language-value" value="en">
                      <button
                        class="header-lang-switcher__trigger"
                        id="header-language-trigger"
                        type="button"
                        aria-haspopup="listbox"
                        aria-expanded="false"
                        aria-label="Select language">
                        <span class="header-lang-switcher__current">
                          <span class="header-lang-switcher__flag flag-en" data-language-flag-current aria-hidden="true"></span>
                          <span class="header-area-1__language-label">EN</span>
                        </span>
                        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                      </button>
                      <div class="header-lang-switcher__menu" id="header-language-list" role="listbox" aria-label="Language options">
                        <button type="button" class="header-lang-switcher__option header-area-1__language-option is-active" data-value="en" data-label="EN" data-flag-class="flag-en" aria-selected="true">
                          <span class="header-lang-switcher__flag flag-en" aria-hidden="true"></span>
                          <span>EN</span>
                        </button>
                        <button type="button" class="header-lang-switcher__option header-area-1__language-option" data-value="ar" data-label="AR" data-flag-class="flag-ar" aria-selected="false">
                          <span class="header-lang-switcher__flag flag-ar" aria-hidden="true"></span>
                          <span>AR</span>
                        </button>
                        <button type="button" class="header-lang-switcher__option header-area-1__language-option" data-value="fr" data-label="FR" data-flag-class="flag-fr" aria-selected="false">
                          <span class="header-lang-switcher__flag flag-fr" aria-hidden="true"></span>
                          <span>FR</span>
                        </button>
                        <button type="button" class="header-lang-switcher__option header-area-1__language-option" data-value="es" data-label="ES" data-flag-class="flag-es" aria-selected="false">
                          <span class="header-lang-switcher__flag flag-es" aria-hidden="true"></span>
                          <span>ES</span>
                        </button>
                      </div>
                    </div>
                    <div id="google_translate_element" class="header-lang-google-translate" aria-hidden="true"></div>
                  </div>

                  <!-- Search Button -->
                  <div class="header__search">
                    <button class="search-open-btn" type="button" aria-expanded="false" aria-controls="site-search">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M23.707 22.293L16.882 15.468C18.204 13.835 19 11.76 19 9.50002C19 4.26202 14.738 0 9.49997 0C4.26197 0 0 4.26197 0 9.49997C0 14.738 4.26202 19 9.50002 19C11.76 19 13.835 18.204 15.468 16.882L22.293 23.707C22.488 23.902 22.744 24 23 24C23.256 24 23.512 23.902 23.707 23.707C24.098 23.316 24.098 22.684 23.707 22.293ZM9.50002 17C5.364 17 2.00002 13.636 2.00002 9.49997C2.00002 5.36395 5.364 1.99997 9.50002 1.99997C13.636 1.99997 17 5.36395 17 9.49997C17 13.636 13.636 17 9.50002 17Z"
                          fill="#070713" />
                      </svg>
                    </button>
                    <div id="site-search" class="search-panel" role="dialog" aria-hidden="true"
                      aria-label="Site search">
                      <div class="search-backdrop"></div>
                      <div class="search-inner" role="document">
                        <button class="search-close" type="button" aria-label="Close search">&times;</button>
                        <form class="search-form" action="<?php echo htmlspecialchars(url('shop.php'), ENT_QUOTES, 'UTF-8'); ?>" method="get" role="search">
                          <input type="search" name="q" class="search-input" placeholder="Search..."
                            autocomplete="off" />
                          <button type="submit" class="search-submit" aria-label="Submit search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>

                  
                  <!-- Wishlist Icon -->
                  <a href="<?php echo url('wishlist.php'); ?>" class="action-btn d-none d-lg-flex position-relative" aria-label="Wishlist">
                    <i class="fa-solid fa-heart"></i>
                    <span
                      class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light wishlist-count"
                      data-wishlist-count
                      style="font-size: 10px; padding: 4px 6px; display:none;">
                      0
                    </span>
                  </a>

                  <!-- Cart Icon with Badge -->
                  <a href="<?php echo url('cart.php'); ?>" class="action-btn position-relative" aria-label="Cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span
                      class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light"
                      data-cart-count
                      data-cart-server="1"
                      style="font-size: 10px; padding: 4px 6px; <?php echo $headerCartCount > 0 ? '' : 'display:none;'; ?>">
                      <?php echo (int) $headerCartCount; ?>
                    </span>
                  </a>

                  <!-- Mobile Menu Toggle -->
                  <div class="header__navicon d-xl-none">
                    <div class="side-toggle">
                      <a class="bar-icon" href="javascript:void(0)">
                        <span></span>
                        <span></span>
                        <span></span>
                      </a>
                    </div>
                  </div>

                  <!-- User Icon with Dropdown -->
                  <div class="user-dropdown-container">
                    <button class="action-btn d-none d-md-flex" id="user-dropdown-toggle" aria-label="User" aria-expanded="false" aria-haspopup="true">
                      <i class="fa-solid fa-user"></i>
                    </button>
                    <div class="user-dropdown-menu" id="user-dropdown-menu" role="menu">
                      <?php
                      $currentUser = user_current();
                      if ($currentUser):
                      ?>
                        <div class="dropdown-header">
                          <div class="user-name"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></div>
                          <div class="user-email"><?php echo htmlspecialchars($currentUser['email']); ?></div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo url('user-dashboard.php'); ?>" class="dropdown-item" role="menuitem">
                          <i class="fa-solid fa-tachometer-alt"></i>
                          Dashboard
                        </a>
                        <a href="<?php echo url('user-orders.php'); ?>" class="dropdown-item" role="menuitem">
                          <i class="fa-solid fa-shopping-bag"></i>
                          Orders
                        </a>
                        <a href="<?php echo url('user-profile.php'); ?>" class="dropdown-item" role="menuitem">
                          <i class="fa-solid fa-user"></i>
                          Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo url('logout.php'); ?>" class="dropdown-item logout-item" role="menuitem">
                          <i class="fa-solid fa-sign-out-alt"></i>
                          Sign Out
                        </a>
                      <?php else: ?>
                        <a href="<?php echo url('login.php'); ?>" class="dropdown-item" role="menuitem">
                          <i class="fa-solid fa-sign-in-alt"></i>
                          Sign In
                        </a>
                        <a href="<?php echo url('register.php'); ?>" class="dropdown-item" role="menuitem">
                          <i class="fa-solid fa-user-plus"></i>
                          Register
                        </a>
                      <?php endif; ?>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </header>
      <!-- Header area end -->

      <main>
