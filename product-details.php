<?php
require_once __DIR__ . '/includes/catalog.php';
require_once __DIR__ . '/includes/content-loader.php';

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$product = $slug !== '' ? catalog_find_product($slug) : null;
if (!$product) {
  $allProducts = catalog_products();
  $product = $allProducts[0] ?? null;
}

$categoryInfo = $product ? catalog_find_category((string) $product['category']) : null;
$currentProductSlug = (string) ($product['slug'] ?? '');

$gallery = [];
$attributes = [];
$shortDescriptionHtml = '<p>Product short description.</p>';
$productDescriptionHtml = '<p>Product description.</p>';
if ($product) {
  if (!empty($product['gallery']) && is_array($product['gallery'])) {
    $gallery = array_values(array_filter($product['gallery'], static function ($img): bool {
      return is_string($img) && trim($img) !== '';
    }));
  }
  if (count($gallery) === 0) {
    $gallery[] = (string) $product['image'];
  }
  if (!empty($product['attributes']) && is_array($product['attributes'])) {
    $attributes = array_values(array_filter($product['attributes'], static function ($row): bool {
      return is_array($row) && trim((string) ($row['key'] ?? '')) !== '' && trim((string) ($row['value'] ?? '')) !== '';
    }));
  }

  $rawShortDescription = (string) ($product['short_description'] ?? '');
  if ($rawShortDescription !== '') {
    $decodedShort = html_entity_decode($rawShortDescription, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $shortDescriptionHtml = sanitize_rich_html($decodedShort);
  }

  $rawDescription = (string) ($product['description'] ?? '');
  if ($rawDescription !== '') {
    $decodedDescription = html_entity_decode($rawDescription, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $productDescriptionHtml = sanitize_rich_html($decodedDescription);
  }
}

if ($gallery === []) {
  $gallery[] = 'assets/imgs/inner/product-details/product-details-thumb1_1.jpg';
}

$meta = [
  'title' => $product ? ('Mybrandplease | ' . $product['name']) : 'Mybrandplease | product details',
  'description' => $product['description'] ?? 'Mybrandplease - product details page',
  'canonical' => $product ? ('product-details.php?slug=' . urlencode((string) $product['slug'])) : 'product-details.php'
];
include 'includes/head.php';
include 'includes/header.php';
?>


        <!--===== Breadcrumb  Section   S T A R T =====-->
        <div class="breadcumb2 fix">
          <div class="container rr-container-1350">
            <div class="breadcumb2-wrapper">
              <ul class="breadcumb2-wrapper__items">
                <li class="breadcumb2-wrapper__items-list">
                  <i class="fa-regular fa-house"></i>
                </li>
                <li class="breadcumb2-wrapper__items-list">
                  <i class="fa-regular fa-chevron-right"></i>
                </li>
                <li class="breadcumb2-wrapper__items-list">
                  <a href="<?php echo htmlspecialchars(url('shop.php' . ($product ? ('?category=' . urlencode((string) $product['category'])) : '')), ENT_QUOTES, 'UTF-8'); ?>" class="breadcumb2-wrapper__items-list-title">
                    <?php echo htmlspecialchars($categoryInfo['name'] ?? 'Category', ENT_QUOTES, 'UTF-8'); ?>
                  </a>
                </li>
                <li class="breadcumb2-wrapper__items-list">
                  <i class="fa-regular fa-chevron-right"></i>
                </li>
                <li class="breadcumb2-wrapper__items-list">
                  <a href="<?php echo htmlspecialchars(url('product-details.php' . ($product ? ('?slug=' . urlencode((string) $product['slug'])) : '')), ENT_QUOTES, 'UTF-8'); ?>" class="breadcumb2-wrapper__items-list-title2">
                    <?php echo htmlspecialchars($product['name'] ?? 'Product Details', ENT_QUOTES, 'UTF-8'); ?>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!--===== Product-Details  Section   S T A R T =====-->
        <section class="product-details section-spacing-120 rr-ov-hidden">
          <div class="container rr-container-1350">
            <div class="product-details-wrapper">
              <div class="row g-4 d-flex justify-content-center justify-content-between">
                <div class="col-xl-6 col-lg-6">
                  <div class="product-details-items">
                    <div class="tab-content">
                      <?php foreach ($gallery as $index => $imagePath): ?>
                        <?php $thumbId = 'thumb-' . ($index + 1); ?>
                        <div id="<?php echo htmlspecialchars($thumbId, ENT_QUOTES, 'UTF-8'); ?>" class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>" role="tabpanel">
                          <div class="product-details-thumb">
                            <div class="thumb">
                              <img src="<?php echo htmlspecialchars(url((string) $imagePath), ENT_QUOTES, 'UTF-8'); ?>"
                                alt="<?php echo htmlspecialchars($product['name'] ?? 'shop-details', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="content">
                              <span class="sale">In stock</span>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                    <div class="tab-header">
                      <!-- Tabs (thumbnails) -->
                      <ul class="nav border-0" role="tablist" aria-label="Product image thumbnails">
                        <?php foreach ($gallery as $index => $imagePath): ?>
                          <?php
                          $thumbId = 'thumb-' . ($index + 1);
                          $isActive = $index === 0;
                          ?>
                          <li class="<?php echo $isActive ? 'item' : 'tab-header-nav-item'; ?> wow fadeInUp" data-wow-delay=".3s" role="presentation">
                            <a class="<?php echo $isActive ? 'nav-link1 active' : 'nav-link2'; ?>" id="<?php echo htmlspecialchars($thumbId . '-tab', ENT_QUOTES, 'UTF-8'); ?>" href="#<?php echo htmlspecialchars($thumbId, ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="tab"
                              role="tab" aria-controls="<?php echo htmlspecialchars($thumbId, ENT_QUOTES, 'UTF-8'); ?>" aria-selected="<?php echo $isActive ? 'true' : 'false'; ?>"<?php echo $isActive ? '' : ' tabindex="-1"'; ?>>
                              <img src="<?php echo htmlspecialchars(url((string) $imagePath), ENT_QUOTES, 'UTF-8'); ?>"
                                alt="<?php echo htmlspecialchars('Product thumbnail ' . ($index + 1), ENT_QUOTES, 'UTF-8'); ?>">
                            </a>
                          </li>
                        <?php endforeach; ?>
                      </ul>


                    </div>
                  </div>
                </div>
                <div class="col-xl-6 col-lg-6">
                  <div class="product-details-content">
                    <!-- <p class="product-details-content__text">Pelican</p> -->
                    <h1 class="product-details-content__title mb-2"><?php echo htmlspecialchars($product['name'] ?? 'Product', ENT_QUOTES, 'UTF-8'); ?></h1>
                    <div class="product-details-content-items d-flex flex-wrap align-items-center gap-3">
                      <div class="product-details-content__price d-flex align-items-baseline gap-2">
                        <span class="price-now">$<?php echo htmlspecialchars(number_format((float) ($product['price'] ?? 0), 2), ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="price-was">$<?php echo htmlspecialchars(number_format((float) (($product['price'] ?? 0) * 1.6), 2), ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="price-currency">USD</span>
                      </div>
                      <span class="product-details-content__badge-pill">60% OFF</span>
                      <div class="product-details-content__rating d-flex align-items-center">
                        <div class="stars">
                          <span class="star"><i class="fa-solid fa-star fa-fw"></i></span>
                          <span class="star"><i class="fa-solid fa-star fa-fw"></i></span>
                          <span class="star"><i class="fa-solid fa-star fa-fw"></i></span>
                          <span class="star"><i class="fa-solid fa-star fa-fw"></i></span>
                          <span class="star5"><i class="fa-solid fa-star fa-fw"></i></span>
                        </div>
                      </div>
                    </div>
                    <div class="product-details-content__desc"><?php echo $shortDescriptionHtml; ?></div>
                    
                    <div class="product-details-content__info">
                      <p class="label mb-3">Sample Quantity</p>
                      <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="qty">
                          <button class="qty-btn" type="button" aria-label="Decrease">-</button>
                          <span class="qty-val">01</span>
                          <button class="qty-btn" type="button" aria-label="Increase">+</button>
                        </div>
                        
                        <button class="btn-heart js-product-toggle-wishlist" type="button" aria-label="Wishlist"
                          data-product-slug="<?php echo htmlspecialchars((string) ($product['slug'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                          data-product-name="<?php echo htmlspecialchars((string) ($product['name'] ?? 'Product'), ENT_QUOTES, 'UTF-8'); ?>"
                          data-product-price="<?php echo htmlspecialchars((string) ($product['price'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"
                          data-product-image="<?php echo htmlspecialchars(url((string) ($product['image'] ?? 'assets/imgs/inner/product-details/product-details-thumb1_1.jpg')), ENT_QUOTES, 'UTF-8'); ?>"
                          data-product-link="<?php echo htmlspecialchars(url('product-details.php' . (!empty($product['slug']) ? ('?slug=' . urlencode((string) $product['slug'])) : '')), ENT_QUOTES, 'UTF-8'); ?>">
                          <i class="fa-solid fa-heart"></i>
                        </button>
                        
                        <button
                          class="btn-heart product-info-btn"
                          type="button"
                          data-bs-toggle="modal"
                          data-bs-target="#productInfoModal"
                          aria-label="Product info">
                          <i class="fa-solid fa-circle-info"></i>
                        </button>
                      </div>
                      <div class="d-flex flex-wrap align-items-center gap-3 mt-0 mt-lg-4 mb-4">
                        <button class="btn-add js-product-add-to-cart" type="button"
                          data-product-slug="<?php echo htmlspecialchars((string) ($product['slug'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                          data-product-name="<?php echo htmlspecialchars((string) ($product['name'] ?? 'Product'), ENT_QUOTES, 'UTF-8'); ?>"
                          data-product-price="<?php echo htmlspecialchars((string) ($product['price'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"
                          data-product-image="<?php echo htmlspecialchars(url((string) ($product['image'] ?? 'assets/imgs/inner/product-details/product-details-thumb1_1.jpg')), ENT_QUOTES, 'UTF-8'); ?>"
                          data-product-link="<?php echo htmlspecialchars(url('product-details.php' . (!empty($product['slug']) ? ('?slug=' . urlencode((string) $product['slug'])) : '')), ENT_QUOTES, 'UTF-8'); ?>">ADD TO CART
                          <span class="btn-icon" aria-hidden="true"><i
                              class="fa-duotone fa-thin fa-arrow-right-long"></i></span>
                        </button>
                        <button class="btn-buy js-product-buy-now" type="button"
                          data-product-slug="<?php echo htmlspecialchars((string) ($product['slug'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                          data-product-name="<?php echo htmlspecialchars((string) ($product['name'] ?? 'Product'), ENT_QUOTES, 'UTF-8'); ?>"
                          data-product-price="<?php echo htmlspecialchars((string) ($product['price'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"
                          data-product-image="<?php echo htmlspecialchars(url((string) ($product['image'] ?? 'assets/imgs/inner/product-details/product-details-thumb1_1.jpg')), ENT_QUOTES, 'UTF-8'); ?>"
                          data-product-link="<?php echo htmlspecialchars(url('product-details.php' . (!empty($product['slug']) ? ('?slug=' . urlencode((string) $product['slug'])) : '')), ENT_QUOTES, 'UTF-8'); ?>">
                          BUY NOW
                          <span class="btn-icon" aria-hidden="true"><i
                              class="fa-duotone fa-thin fa-arrow-right-long"></i></span>
                        </button>
                        <button
                          class="btn-add js-open-enquiry"
                          type="button"
                          data-product-id="<?php echo htmlspecialchars((string) ($product['slug'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                          ENQUIRY
                          <span class="btn-icon" aria-hidden="true"><i class="fa-solid fa-envelope"></i></span>
                        </button>
                      </div>
                    </div>
                    <div class="product-details-content__meta mb-4">
                      <div class="meta-row"><span class="k">Category:</span> <span class="v"><?php echo htmlspecialchars($categoryInfo['name'] ?? 'Beauty & Cosmetics', ENT_QUOTES, 'UTF-8'); ?></span></div>
                      <!-- <div class="meta-row"><span class="k">Tag:</span> <span class="v">Cream</span></div> -->
                    </div>
                    <!-- <div class="product-details-content__checkout">
                      <p class="product-details-content__checkout-text mb-2">Guranted Safe Checkout</p>
                      <div class="pay-row">
                        <span class="pay-badge"><img src="assets/imgs/inner/product-details/product-details-logo1_1.png"
                            alt="logo"></span>
                        <span class="pay-badge"><img src="assets/imgs/inner/product-details/product-details-logo1_2.png"
                            alt="logo"></span>
                        <span class="pay-badge"><img src="assets/imgs/inner/product-details/product-details-logo1_3.png"
                            alt="logo"></span>
                        <span class="pay-badge"><img src="assets/imgs/inner/product-details/product-details-logo1_4.png"
                            alt="logo"></span>
                        <span class="pay-badge"><img src="assets/imgs/inner/product-details/product-details-logo1_3.png"
                            alt="logo"></span>
                      </div>
                    </div> -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>


        <!--===== Product Tab  Section    S T A R T =====-->
        <div class="product-tab section-spacing-120 rr-ov-hidden pt-0">
          <div class="container rr-container-1350">
            <ul class="nav nav-tabs tab-buttons" id="myTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="one-tab" data-bs-toggle="tab" data-bs-target="#one-tab-pane"
                  type="button" role="tab" aria-controls="one-tab-pane" aria-selected="true">Description
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="two-tab" data-bs-toggle="tab" data-bs-target="#two-tab-pane" type="button"
                  role="tab" aria-controls="two-tab-pane" aria-selected="false">Additional information</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="three-tab" data-bs-toggle="tab" data-bs-target="#three-tab-pane"
                  type="button" role="tab" aria-controls="three-tab-pane" aria-selected="false">Reviews (1)</button>
              </li>
            </ul>
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="one-tab-pane" role="tabpanel" aria-labelledby="one-tab"
                tabindex="0">
                <div class="product-tab-wrapper">
                  <div class="row d-flex justify-content-between">
                    <div class="col-xl-12">
                      <div class="product-tab-card">
                        <div class="product-tab-card__content">
                          <div class="product-tab-card__content-title">Description</div>
                          <div class="product-tab-card__content-dsc"><?php echo $productDescriptionHtml; ?></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="two-tab-pane" role="tabpanel" aria-labelledby="two-tab" tabindex="0">
                <div class="product-tab-wrapper">
                  <div class="row d-flex justify-content-between">
                    <div class="col-xl-12">
                      <div class="product-tab-card">
                        <div class="product-tab-card__content">
                          <div class="product-tab-card__content-title">Additional Information</div>
                          <?php if (!empty($attributes)): ?>
                            <div class="table-responsive">
                              <table class="table table-bordered mb-0">
                                <tbody>
                                  <?php foreach ($attributes as $attr): ?>
                                    <tr>
                                      <th style="width:35%;"><?php echo htmlspecialchars((string) $attr['key'], ENT_QUOTES, 'UTF-8'); ?></th>
                                      <td><?php echo htmlspecialchars((string) $attr['value'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                  <?php endforeach; ?>
                                </tbody>
                              </table>
                            </div>
                          <?php else: ?>
                            <p class="product-tab-card__content-subtitle2">No additional information available for this product.</p>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="three-tab-pane" role="tabpanel" aria-labelledby="three-tab" tabindex="0">
                <div class="product-tab-wrapper">
                  <div class="row g-4 d-flex justify-content-between">
                    <div class="col-xl-7">
                      <div class="product-tab-items">
                        <p class="product-tab-items__text">05 review for Denim Jean Top Jacket Sleeve Crop Women</p>
                        <div class="product-tab-items__card d-flex align-items-start justify-content-between gap-3">

                          <div
                            class="product-tab-items__card-info d-flex align-items-center justify-content-between gap-3">
                            <div class="product-tab-items__card-thumb">
                              <img src="assets/imgs/inner/product-details/image-1.png" alt="img">
                            </div>
                            <div class="product-tab-items__card-info-content">
                              <p class="product-tab-items__card-info-content-text">George – October 13, 2023</p>
                              <div class="product-tab-items__card-info-content-name">Amazing Quility 😍</div>
                            </div>
                          </div>

                          <div class="product-tab-items__card-info-star">
                            <img src="assets/imgs/inner/product-details/star.png" alt="stat">
                          </div>
                        </div>
                        <div class="product-tab-items__card d-flex align-items-start justify-content-between gap-3">
                          <div
                            class="product-tab-items__card-info d-flex align-items-center justify-content-between gap-3">
                            <div class="product-tab-items__card-thumb">
                              <img src="assets/imgs/inner/product-details/image-2.png" alt="img">
                            </div>
                            <div class="product-tab-items__card-info-content">
                              <p class="product-tab-items__card-info-content-text">George – October 13, 2023</p>
                              <div class="product-tab-items__card-info-content-name">Amazing Quility 😍</div>
                            </div>
                          </div>
                          <div class="product-tab-items__card-info-star">
                            <img src="assets/imgs/inner/product-details/star.png" alt="stat">
                          </div>
                        </div>
                        <div class="product-tab-items__card  d-flex align-items-start justify-content-between gap-3">
                          <div
                            class="product-tab-items__card-info d-flex align-items-center justify-content-between gap-3">
                            <div class="product-tab-items__card-thumb">
                              <img src="assets/imgs/inner/product-details/image-3.png" alt="img">
                            </div>
                            <div class="product-tab-items__card-info-content">
                              <p class="product-tab-items__card-info-content-text">George – October 13, 2023</p>
                              <div class="product-tab-items__card-info-content-name">Amazing Quility 😍</div>
                            </div>
                          </div>
                          <div class="product-tab-items__card-info-star">
                            <img src="assets/imgs/inner/product-details/star.png" alt="stat">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-xl-5">
                      <div class="product-tab-contact">
                        <div class="product-tab-contact__title">Add a review</div>
                        <form action="contact.php" id="contact-form" method="POST" class="product-tab-contact__form">
                          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                          <div class="row g-4">
                            <div class="col-lg-12">
                              <div class="product-tab-contact__form_input">
                                <span class="product-tab-contact__form-input-name">Your Name</span>
                                <input type="text" class="product-tab-contact__form-input-field" name="name" id="name"
                                  placeholder="Enter Your Name">
                              </div>
                            </div>
                            <div class="col-lg-12">
                              <div class="product-tab-contact__form_input">
                                <span class="product-tab-contact__form-input-name">Your Email</span>
                                <input type="text" class="product-tab-contact__form-input-field" name="email"
                                  id="email1" placeholder="Email Here">
                              </div>
                            </div>
                            <div class="col-lg-12">
                              <div class="product-tab-contact__form_input">
                                <span class="product-tab-contact__form-input-name">Your Message</span>
                                <textarea name="message" class="product-tab-contact__form-input-field textarea"
                                  id="message" placeholder="Enter Your Message"></textarea>
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <button type="submit" class="rr-btn-button">
                                <span class="text">Send Message</span>
                                <span class="icon">
                                  <svg width="16" height="10" viewBox="0 0 16 10" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                      d="M0.599976 4.59998H14.6M14.6 4.59998L10.6 8.59998M14.6 4.59998L10.6 0.599976"
                                      stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                                    </path>
                                  </svg>
                                </span>
                              </button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!--===== Offertwo Section    S T A R T =====-->
        <section class="featured-products2 section-spacing-120 rr-ov-hidden pt-0">
          <div class="container rr-container-1350">
            <div class="row gy-5 d-flex align-items-center justify-content-between">
              <div class="col-xl-6 d-flex justify-content-start">
                <div class="section-heading">
                  <h2 class="section-heading__title wow fadeInUp" data-wow-delay=".5s"
                    style="visibility: visible; animation-delay: 0.5s; animation-name: fadeInUp;">FEATURED PRODUCTS
                  </h2>
                </div>
              </div>
              <div class="col-xl-6 d-flex justify-content-xl-end">
                <div class="featured-products2-controls wow fadeInUp" data-wow-delay=".5s"
                  style="visibility: visible; animation-delay: 0.5s; animation-name: fadeInUp;">
                  <div class="featured-products2-controls__arrowLeft">
                    <div class="icon"><i class="fa-solid fa-angle-left"></i></div>prev
                  </div>
                  <div class="featured-products2-controls__arrowRight">next
                    <div class="icon"><i class="fa-solid fa-angle-right"></i></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="featured-products2-wrapper">
              <div class="swiper featured-products2-slider" id="related-products-section" data-product-slug="<?php echo htmlspecialchars($currentProductSlug, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="related-products-loader" id="related-products-loader" aria-live="polite">
                  <span class="related-products-loader__spinner" aria-hidden="true"></span>
                  <span>Loading featured products...</span>
                </div>
                <div class="swiper-wrapper" id="related-products-wrapper"></div>
              </div>
            </div>
          </div>
        </section>

      </main>

      <!--===== Footer Section    S T A R T =====-->
      <footer class="footer2" style="background-image: url(assets/imgs/footer/footer-bg2.jpg);">
        <div class="container rr-container-1350">
          <div class="footer2-main style2">
            <div class="row g-5 d-flex justify-content-between">
              <div class="col-xl-5 col-md-7 wow fadeInUp" data-wow-delay=".5s"
                style="visibility: visible; animation-delay: 0.7s; animation-name: fadeInUp;">
                <div class="footer2-widget">
                  <div class="footer2-widget__title">We Shape Beauty Into Extraordinary Skin Experiences.</div>
                  <div class="footer2-widget-button-items">
                    <div class="footer2-widget-button-items-btn-wrapper">
                      <a href="cart.php" class="btn-button2">
                        <span class="text">Downlode For</span>
                        <span class="icon">
                          <i class="fa-brands fa-apple"></i>
                        </span>
                      </a>
                    </div>
                    <div class="footer2-widget-button-items-btn-wrapper">
                      <a href="cart.php" class="btn-button2">
                        <span class="text">Downlode For</span>
                        <span class="icon">
                          <i class="fa-solid fa-play"></i>
                        </span>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-md-5 wow fadeInUp" data-wow-delay=".7s"
                style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInUp;">
                <div class="footer2-widget">
                  <div class="footer2-widget__logo"><img src="assets/imgs/logo/footer-logo.png" alt="logo"></div>
                  <div class="footer2-widget__social">
                    <div class="footer2-widget__social-title">Social Icon</div>
                    <div class="footer2-widget__social-link">
                      <a href="contact.php"> <span><i class="fa-brands fa-facebook-f"></i></span> </a>
                      <a href="contact.php"> <span><i class="fa-brands fa-twitter"></i></span> </a>
                      <a href="contact.php"> <span><i class="fa-brands fa-linkedin-in"></i></span> </a>
                      <a href="contact.php"> <span><i class="fa-brands fa-instagram"></i></span> </a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 col-md-6 wow fadeInUp" data-wow-delay=".9s">
                <div class="footer2-widget ps-xxl-3 ms-xxl-3">
                  <div class="footer2-widget__contact">
                    <div class="footer2-widget__contact-post">
                      <div class="footer2-widget__contact-post-title">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M18.9018 7.74567C17.9393 3.51067 14.2451 1.604 11.0001 1.604C11.0001 1.604 11.0001 1.604 10.9909 1.604C7.75509 1.604 4.05175 3.5015 3.08925 7.7365C2.01675 12.4665 4.91342 16.4723 7.53509 18.9932C8.50675 19.9282 9.75342 20.3957 11.0001 20.3957C12.2468 20.3957 13.4934 19.9282 14.4559 18.9932C17.0776 16.4723 19.9743 12.4757 18.9018 7.74567ZM11.0001 12.3382C9.40509 12.3382 8.11259 11.0457 8.11259 9.45067C8.11259 7.85567 9.40509 6.56317 11.0001 6.56317C12.5951 6.56317 13.8876 7.85567 13.8876 9.45067C13.8876 11.0457 12.5951 12.3382 11.0001 12.3382Z"
                            fill="#EE2D7A"></path>
                        </svg>
                        Home Address
                      </div>
                      <a class="footer2-widget__contact-post-link" href="contact.php">
                        Mary Johnson. RR 2, Box 10, Lancaster, PA 17602
                      </a>
                    </div>
                    <div class="footer2-widget__contact-items">
                      <div class="footer2-widget__contact-post">
                        <div class="footer2-widget__contact-post-title">
                          <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                              d="M18.9018 7.74567C17.9393 3.51067 14.2451 1.604 11.0001 1.604C11.0001 1.604 11.0001 1.604 10.9909 1.604C7.75509 1.604 4.05175 3.5015 3.08925 7.7365C2.01675 12.4665 4.91342 16.4723 7.53509 18.9932C8.50675 19.9282 9.75342 20.3957 11.0001 20.3957C12.2468 20.3957 13.4934 19.9282 14.4559 18.9932C17.0776 16.4723 19.9743 12.4757 18.9018 7.74567ZM11.0001 12.3382C9.40509 12.3382 8.11259 11.0457 8.11259 9.45067C8.11259 7.85567 9.40509 6.56317 11.0001 6.56317C12.5951 6.56317 13.8876 7.85567 13.8876 9.45067C13.8876 11.0457 12.5951 12.3382 11.0001 12.3382Z"
                              fill="#EE2D7A"></path>
                          </svg>
                          Email Address
                        </div>
                        <a class="footer2-widget__contact-post-link" href="tel:infor@beautengmail.com">
                          infor@beautengmail.com
                        </a>
                      </div>
                      <div class="footer2-widget__contact-post">
                        <div class="footer2-widget__contact-post-title">
                          <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                              d="M18.9018 7.74567C17.9393 3.51067 14.2451 1.604 11.0001 1.604C11.0001 1.604 11.0001 1.604 10.9909 1.604C7.75509 1.604 4.05175 3.5015 3.08925 7.7365C2.01675 12.4665 4.91342 16.4723 7.53509 18.9932C8.50675 19.9282 9.75342 20.3957 11.0001 20.3957C12.2468 20.3957 13.4934 19.9282 14.4559 18.9932C17.0776 16.4723 19.9743 12.4757 18.9018 7.74567ZM11.0001 12.3382C9.40509 12.3382 8.11259 11.0457 8.11259 9.45067C8.11259 7.85567 9.40509 6.56317 11.0001 6.56317C12.5951 6.56317 13.8876 7.85567 13.8876 9.45067C13.8876 11.0457 12.5951 12.3382 11.0001 12.3382Z"
                              fill="#EE2D7A"></path>
                          </svg>
                          Email Address
                        </div>
                        <a class="footer2-widget__contact-post-link" href="tel:infor@beautengmail.com">
                          infor@beautengmail.com
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="footer2-bottom">
          <div class="container rr-container-1350">
            <div class="footer2-bottom__wrapper">
              <div class="footer2-bottom__copyright wow fadeInLeft" data-wow-delay=".9s">© 2026<a href="index.php">
                  BeauTen</a>. All Rights Reserved.</div>
              <div class="footer2-bottom__navs wow fadeInRight" data-wow-delay=".9s">
                <a href="contact.php">Terms of Services</a>
                <a href="contact.php">Privacy Policy</a>
                <a href="contact.php">Cookie Policy</a>
              </div>
            </div>
          </div>
        </div>
      </footer>

    </div>
  </div>


  <!-- Dependencies JS Files -->
  <script src="assets/vandor/jquery/jquery.js"></script>
  <script src="assets/vandor/bootstrap/bootstrap.bundle.min.js"></script>
  <script src="assets/vandor/popup/jquery.magnific-popup.min.js"></script>
  <script src="assets/vandor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vandor/gsap/gsap.min.js"></script>
  <script src="assets/vandor/gsap/ScrollSmoother.min.js"></script>
  <script src="assets/vandor/gsap/ScrollTrigger.min.js"></script>
  <script src="assets/vandor/gsap/SplitText.min.js"></script>
  <script src="assets/vandor/gsap/SplitType.js"></script>
  <script src="assets/vandor/gsap/customEase.js"></script>
  <script src="assets/vandor/odometer/odometer.min.js"></script>
  <script src="assets/vandor/odometer/waypoints.min.js"></script>
  <script src="assets/vandor/menu/jquery.meanmenu.min.js"></script>
  <script src="assets/vandor/backtop/backToTop.js"></script>
  <script src="assets/vandor/nice-select/nice-select.js"></script>
  <script src="assets/vandor/wow/wow.min.js"></script>
  <!-- Template Main JS File -->
  <script src="assets/vandor/common-js/common.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/categories-sidebar.js"></script>

  <!-- Color Selection Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Color Selection Functionality
      const colorDots = document.querySelectorAll('.color-dot');
      const selectedDot = document.querySelector('.selected-dot');
      const selectedName = document.querySelector('.selected-name');

      colorDots.forEach(function (dot) {
        dot.addEventListener('click', function () {
          // Remove active class from all dots
          colorDots.forEach(function (d) {
            d.classList.remove('is-active');
          });

          // Add active class to clicked dot
          this.classList.add('is-active');

          // Update selected color display
          const colorValue = this.style.getPropertyValue('--dot');
          const colorLabel = this.getAttribute('aria-label');

          if (selectedDot) {
            selectedDot.style.background = colorValue;
          }

          if (selectedName) {
            selectedName.textContent = colorLabel.toUpperCase();
          }
        });
      });

      // Quantity Selector Functionality
      const qtyDecreaseBtn = document.querySelector('.qty-btn[aria-label="Decrease"]');
      const qtyIncreaseBtn = document.querySelector('.qty-btn[aria-label="Increase"]');
      const qtyVal = document.querySelector('.qty-val');

      if (qtyDecreaseBtn && qtyIncreaseBtn && qtyVal) {
        let currentQty = parseInt(qtyVal.textContent.trim()) || 1;
        const minQty = 1;
        const maxQty = 99;

        // Function to update quantity display
        function updateQty(value) {
          currentQty = Math.max(minQty, Math.min(maxQty, value));
          qtyVal.textContent = String(currentQty).padStart(2, '0');

          // Update button states for visual feedback
          qtyDecreaseBtn.disabled = currentQty <= minQty;
          qtyIncreaseBtn.disabled = currentQty >= maxQty;

          // Add visual feedback
          qtyVal.style.transform = 'scale(1.1)';
          setTimeout(function () {
            qtyVal.style.transform = 'scale(1)';
          }, 200);
        }

        // Initialize button states
        updateQty(currentQty);

        // Decrease button
        qtyDecreaseBtn.addEventListener('click', function (e) {
          e.preventDefault();
          if (currentQty > minQty) {
            updateQty(currentQty - 1);
          }
        });

        // Increase button
        qtyIncreaseBtn.addEventListener('click', function (e) {
          e.preventDefault();
          if (currentQty < maxQty) {
            updateQty(currentQty + 1);
          }
        });
      }


      // Product image zoom on cursor move
      const zoomThumbs = document.querySelectorAll('.product-details-thumb .thumb');
      zoomThumbs.forEach(function (thumb) {
        const img = thumb.querySelector('img');
        if (!img) return;

        thumb.addEventListener('mousemove', function (e) {
          const rect = thumb.getBoundingClientRect();
          const x = ((e.clientX - rect.left) / rect.width) * 100;
          const y = ((e.clientY - rect.top) / rect.height) * 100;
          img.style.transformOrigin = x + '% ' + y + '%';
          img.style.transform = 'scale(1.22)';
        });

        thumb.addEventListener('mouseleave', function () {
          img.style.transformOrigin = 'center center';
          img.style.transform = 'scale(1)';
        });
      });

      // Thumbnail click -> show selected large image pane
      const thumbLinks = document.querySelectorAll('.product-details .tab-header .nav a[href^="#thumb-"]');
      const thumbPanes = document.querySelectorAll('.product-details .tab-content .tab-pane');
      thumbLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
          e.preventDefault();
          const targetId = this.getAttribute('href');
          if (!targetId) return;
          const targetPane = document.querySelector(targetId);
          if (!targetPane) return;

          thumbPanes.forEach(function (pane) {
            pane.classList.remove('show', 'active');
          });
          targetPane.classList.add('show', 'active');

          thumbLinks.forEach(function (a) {
            a.classList.remove('active');
            a.setAttribute('aria-selected', 'false');
          });
          this.classList.add('active');
          this.setAttribute('aria-selected', 'true');
        });
      });

      // Helper function to show notifications
      function showNotification(message, type) {
        // Remove existing notification if any
        const existingNotification = document.querySelector('.product-notification');
        if (existingNotification) {
          existingNotification.remove();
        }

        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'product-notification';
        notification.textContent = message;
        notification.style.cssText = `
          position: fixed;
          top: 20px;
          right: 20px;
          background: ${type === 'success' ? '#4CAF50' : type === 'info' ? '#2196F3' : '#f44336'};
          color: white;
          padding: 15px 25px;
          border-radius: 8px;
          box-shadow: 0 4px 6px rgba(0,0,0,0.1);
          z-index: 10000;
          font-family: var(--font_Lato);
          font-size: 14px;
          animation: slideIn 0.3s ease;
        `;

        // Add animation
        const style = document.createElement('style');
        style.textContent = `
          @keyframes slideIn {
            from {
              transform: translateX(100%);
              opacity: 0;
            }
            to {
              transform: translateX(0);
              opacity: 1;
            }
          }
        `;
        document.head.appendChild(style);

        document.body.appendChild(notification);

        // Remove notification after 3 seconds
        setTimeout(function () {
          notification.style.animation = 'slideIn 0.3s ease reverse';
          setTimeout(function () {
            notification.remove();
          }, 300);
        }, 3000);
      }

    });
  </script>
  <script>
    // Fallback shared store for product-details page (this page does not use includes/footer.php).
    if (!window.MybrandStore) {
      window.MybrandStore = (function () {
        const CART_KEY = 'cart';
        const WISHLIST_KEY = 'wishlist';

        function read(key) {
          try {
            const value = JSON.parse(localStorage.getItem(key) || '[]');
            if (Array.isArray(value)) return value;
            if (value && typeof value === 'object') return Object.values(value);
            return [];
          } catch (error) {
            return [];
          }
        }

        function write(key, items) {
          localStorage.setItem(key, JSON.stringify(items));
          if (key === WISHLIST_KEY) {
            syncWishlistToServer(items);
          }
          window.dispatchEvent(new CustomEvent('mybrand:store-updated', { detail: { key: key, items: items } }));
        }

        function syncWishlistToServer(items) {
          try {
            fetch('<?php echo url('api/wishlist.php'); ?>', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: JSON.stringify({
                action: 'replace',
                items: Array.isArray(items) ? items : []
              })
            }).catch(function () {});
          } catch (error) {
            // Ignore sync failures for guests/offline.
          }
        }

        function syncWishlistFromServer() {
          try {
            fetch('<?php echo url('api/wishlist.php'); ?>', {
              method: 'GET',
              headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
              if (!data || !data.success || !data.data || !Array.isArray(data.data.items)) {
                return;
              }
              const localItems = read(WISHLIST_KEY);
              const serverItems = data.data.items;
              if (Array.isArray(localItems) && localItems.length > 0 && serverItems.length === 0) {
                syncWishlistToServer(localItems);
                window.dispatchEvent(new CustomEvent('mybrand:store-updated', { detail: { key: WISHLIST_KEY, items: localItems } }));
                return;
              }
              localStorage.setItem(WISHLIST_KEY, JSON.stringify(serverItems));
              window.dispatchEvent(new CustomEvent('mybrand:store-updated', { detail: { key: WISHLIST_KEY, items: serverItems } }));
            })
            .catch(function () {});
          } catch (error) {
            // Ignore sync failures for guests/offline.
          }
        }

        function normalize(product) {
          const parsedPrice = parseFloat(String(product.price ?? 0).replace(/[^0-9.]/g, ''));
          return {
            slug: String(product.slug || '').trim(),
            title: String(product.title || 'Product').trim(),
            price: Number.isFinite(parsedPrice) ? parsedPrice : 0,
            image: String(product.image || '').trim(),
            link: String(product.link || 'product-details.php').trim(),
            quantity: Math.max(1, parseInt(product.quantity || 1, 10) || 1)
          };
        }

        return {
          getCart: function () { return read(CART_KEY); },
          getWishlist: function () { return read(WISHLIST_KEY); },
          addToCart: function (product) {
            const item = normalize(product);
            const cart = read(CART_KEY);
            const existing = cart.find(function (entry) { return entry.slug !== '' && entry.slug === item.slug; });
            if (existing) {
              existing.quantity = Math.max(1, parseInt(existing.quantity || 1, 10) || 1) + item.quantity;
            } else {
              cart.push(item);
            }
            write(CART_KEY, cart);
            return cart;
          },
          toggleWishlist: function (product) {
            const item = normalize(product);
            const wishlist = read(WISHLIST_KEY);
            const index = wishlist.findIndex(function (entry) { return entry.slug !== '' && entry.slug === item.slug; });
            if (index >= 0) {
              wishlist.splice(index, 1);
              write(WISHLIST_KEY, wishlist);
              return false;
            }
            wishlist.push(item);
            write(WISHLIST_KEY, wishlist);
            return true;
          },
          isInWishlist: function (slug) {
            return read(WISHLIST_KEY).some(function (item) { return item.slug === slug; });
          },
          syncWishlistFromServer: syncWishlistFromServer
        };
      })();
      window.MybrandStore.syncWishlistFromServer();
    }

    document.addEventListener('DOMContentLoaded', function () {
      const store = window.MybrandStore;
      const qtyVal = document.querySelector('.qty-val');
      const addBtn = document.querySelector('.js-product-add-to-cart');
      const wishlistBtn = document.querySelector('.js-product-toggle-wishlist');
      const buyBtn = document.querySelector('.js-product-buy-now');

      function getPayload(trigger) {
        const quantity = parseInt(qtyVal?.textContent.trim() || '1', 10);
        return {
          slug: trigger?.dataset.productSlug || '',
          title: trigger?.dataset.productName || 'Product',
          price: Number(trigger?.dataset.productPrice || 0),
          image: trigger?.dataset.productImage || '',
          link: trigger?.dataset.productLink || window.location.href,
          quantity: Number.isFinite(quantity) && quantity > 0 ? quantity : 1
        };
      }

      function toast(message, type) {
        const existing = document.querySelector('.product-notification');
        if (existing) existing.remove();
        const notification = document.createElement('div');
        notification.className = 'product-notification';
        notification.textContent = message;
        notification.style.cssText = 'position:fixed;top:20px;right:20px;background:' + (type === 'success' ? '#4CAF50' : '#2196F3') + ';color:#fff;padding:15px 25px;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,.1);z-index:10000;font-family:var(--font_Lato);font-size:14px;';
        document.body.appendChild(notification);
        setTimeout(function () { notification.remove(); }, 2500);
      }

      function syncWishlistState() {
        if (!wishlistBtn || !store) return;
        wishlistBtn.classList.toggle('active', store.isInWishlist(wishlistBtn.dataset.productSlug || ''));
      }

      if (addBtn) {
        addBtn.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopImmediatePropagation();
          const slug = (addBtn.dataset.productSlug || '').trim();
          if (!slug) return;
          window.location.href = `<?php echo url('cart.php'); ?>?add=${encodeURIComponent(slug)}`;
        }, true);
      }

      if (wishlistBtn && store) {
        syncWishlistState();
        wishlistBtn.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopImmediatePropagation();
          const added = store.toggleWishlist(getPayload(wishlistBtn));
          syncWishlistState();
          toast(added ? 'Added to wishlist!' : 'Removed from wishlist', 'success');
        }, true);
      }

      if (buyBtn) {
        buyBtn.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopImmediatePropagation();
          const slug = (buyBtn.dataset.productSlug || '').trim();
          if (!slug) return;
          window.location.href = `<?php echo url('cart.php'); ?>?add=${encodeURIComponent(slug)}`;
        }, true);
      }

      (function () {
        const modal = document.getElementById('enquiry-modal');
        const productInput = document.getElementById('enquiry-product-id');
        if (!modal || !productInput) return;

        function openModal(productId) {
          productInput.value = productId || '';
          modal.classList.add('is-open');
          modal.setAttribute('aria-hidden', 'false');
          document.body.style.overflow = 'hidden';
        }

        function closeModal() {
          modal.classList.remove('is-open');
          modal.setAttribute('aria-hidden', 'true');
          document.body.style.overflow = '';
        }

        document.querySelectorAll('.js-open-enquiry').forEach(function (button) {
          button.addEventListener('click', function () {
            openModal((this.dataset.productId || '').trim());
          });
        });

        modal.querySelectorAll('[data-enquiry-close]').forEach(function (trigger) {
          trigger.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
          }
        });
      })();
    });
  </script>

  <div class="enquiry-modal" id="enquiry-modal" aria-hidden="true">
    <div class="enquiry-modal__backdrop" data-enquiry-close></div>
    <div class="enquiry-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="enquiry-modal-title">
      <button type="button" class="enquiry-modal__close" data-enquiry-close aria-label="Close enquiry form">&times;</button>
      <h3 class="enquiry-modal__title" id="enquiry-modal-title">Product Enquiry</h3>
      <form class="enquiry-modal__form" method="post" action="<?php echo htmlspecialchars(url('contact.php'), ENT_QUOTES, 'UTF-8'); ?>">
        <label class="enquiry-modal__field">
          <span>Product ID</span>
          <input type="text" name="product_id" id="enquiry-product-id" readonly required>
        </label>
        <label class="enquiry-modal__field">
          <span>Name</span>
          <input type="text" name="name" required>
        </label>
        <label class="enquiry-modal__field">
          <span>Email</span>
          <input type="email" name="email" required>
        </label>
        <label class="enquiry-modal__field">
          <span>Phone</span>
          <input type="text" name="phone" required>
        </label>
        <label class="enquiry-modal__field">
          <span>Address</span>
          <textarea name="address" rows="2" required></textarea>
        </label>
        <label class="enquiry-modal__field">
          <span>Bulk Quantity</span>
          <input type="number" min="1" name="bulk_quantity" required>
        </label>
        <button type="submit" class="enquiry-modal__submit">Submit Enquiry</button>
      </form>
    </div>
  </div>

  <div class="modal fade" id="productInfoModal" tabindex="-1" aria-labelledby="productInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content product-info-modal">
        <div class="modal-header">
          <h5 class="modal-title" id="productInfoModalLabel">Product Information</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <div class="nav flex-column nav-pills product-info-tabs" id="product-info-tab" role="tablist" aria-orientation="vertical">
                <button class="nav-link active" id="product-info-moq-tab" data-bs-toggle="pill" data-bs-target="#product-info-moq" type="button" role="tab" aria-controls="product-info-moq" aria-selected="true">MOQ</button>
                <button class="nav-link" id="product-info-note-tab" data-bs-toggle="pill" data-bs-target="#product-info-note" type="button" role="tab" aria-controls="product-info-note" aria-selected="false">Important Note</button>
                <button class="nav-link" id="product-info-custom-tab" data-bs-toggle="pill" data-bs-target="#product-info-custom" type="button" role="tab" aria-controls="product-info-custom" aria-selected="false">New Custom Formulation</button>
              </div>
            </div>
            <div class="col-md-8">
              <div class="tab-content" id="product-info-tabContent">
                <div class="tab-pane fade show active" id="product-info-moq" role="tabpanel" aria-labelledby="product-info-moq-tab" tabindex="0">
                  <h6 class="mb-2">Minimum Order Quantity</h6>
                  <p class="mb-2 text-muted lh-base fs-17 word-spacing-3">Our MOQ is usually based on bulk batch sizes ranging from 50 kg to 100 kg Bulk, depending on the viscosity and nature of the product. (�10% tolerance).</p>
                  <p class="mb-2 text-muted lh-base fs-17 word-spacing-3">Eg - For a 50 kg Bulk batch size usually for skin care or hair care Serums & color cosmetics, if your pack size is 30ml, you would receive approximately 1,000 to 1500 units. (�10% tolerance).</p>
                  <p class="mb-2 text-muted lh-base fs-17 word-spacing-3">For a batch size of 100 kg or more, typically for  Cream, SPF�s, Shampoo, Conditioners, Body Lotions, Body Wash, Hair oil Variants, etc., a 200 ml SKU would yield approximately 500 units. (�10% tolerance).</p>
                </div>
                <div class="tab-pane fade" id="product-info-note" role="tabpanel" aria-labelledby="product-info-note-tab" tabindex="0">
                  <h6 class="mb-2">Why is adhering to MOQ important?</h6>
                  <p class="mb-2 text-muted lh-base fs-17 word-spacing-3">The Minimum Order Quantity for bulk batch size that we have shared is the industry standard globally, and it is set to ensure product quality, stability, and consistent efficacy for commercial production batches. Bulk sizes below this threshold is very likely compromising on formulation integrity.</p>
                  <p class="mb-2 text-muted lh-base fs-17 word-spacing-3">For cosmetic products especially those with performance-oriented actives the shelf life, texture, fragrance stability, and overall efficacy depend heavily on proper homogenization. In standard manufacturing, ingredients are blended using automated mixers at controlled RPM, which vary according to the viscosity and nature of each formulation. This ensures uniform distribution of active ingredients and guarantees compliance with product claims.</p>
                  <p class="mb-2 text-muted lh-base fs-17 word-spacing-3">When the batch size is reduced below the minimum required volume, the blending process cannot be executed correctly within the mixer. This results in uneven dispersion of actives, compromised stability, and a product whose shelf life cannot be assured. Manual blending is not a sustainable or scientifically sound method for commercial production, especially for premium grooming products.</p>
                  <p class="mb-2 text-muted lh-base fs-17 word-spacing-3">For these reasons, adhering to the industry-standard MOQ is essential to deliver a product that meets both regulatory expectations and your brand�s high-performance standards.</p>
                </div>
                <div class="tab-pane fade" id="product-info-custom" role="tabpanel" aria-labelledby="product-info-custom-tab" tabindex="0">
                  <h6 class="mb-2">New Custom Formulation</h6>
                  <p class="mb-2 text-muted lh-base fs-17 word-spacing-3">For fully customized product development, we charge USD 250 development fee, which includes:</p>
                  <h6 class="mb-2">Complete formulation development</h6>
                  <ul>
                    <li class="mb-2 text-muted lh-base fs-17 word-spacing-3">Up to three revision rounds</li>
                    <li class="mb-2 text-muted lh-base fs-17 word-spacing-3">Complimentary sample shipping.</li>
                    <li class="mb-2 text-muted lh-base fs-17 word-spacing-3">Custom formulation development takes approximately 5 to 15 working days, depending on the complexity of the formulation.</li>
                  </ul>
                  
                  <h6 class="mb-2">The best part?</h6>
                  <p class="mb-2 text-muted lh-base fs-17 word-spacing-3">Once you confirm your production order, the full $250 is credited to your first invoice, making your development process virtually risk-free.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    (function () {
      function escHtml(value) {
        return String(value ?? '')
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }
      var imageBase = '<?php echo htmlspecialchars(base_url(), ENT_QUOTES, 'UTF-8'); ?>/mybrand/';
      var detailsBase = '<?php echo htmlspecialchars(url('product-details.php?slug='), ENT_QUOTES, 'UTF-8'); ?>';

      function renderRelated(items, wrapper) {
        if (!Array.isArray(items) || items.length === 0) {
          wrapper.innerHTML = '' +
            '<div class="swiper-slide">' +
            '  <div class="featured-products2-card">' +
            '    <div class="featured-products2-card__content">' +
            '      <div class="featured-products2-card__content-title">No featured products available.</div>' +
            '    </div>' +
            '  </div>' +
            '</div>';
          return;
        }

        wrapper.innerHTML = items.map(function (rp) {
          var slug = encodeURIComponent(String(rp.slug || ''));
          var image = escHtml(String(rp.image || 'assets/imgs/inner/featured-products/featured-products-thumb1_1.jpg'));
          var name = escHtml(String(rp.name || 'Product'));
          var rating = Number(rp.rating || 0).toFixed(1);
          var reviews = escHtml(String(rp.reviews || 0));
          var price = Number(rp.price || 0).toFixed(2);

          return '' +
            '<div class="swiper-slide">' +
            '  <div class="featured-products2-card">' +
            '    <div class="featured-products2-card__thumb">' +
            '      <img src="' + (image.indexOf('http') === 0 ? image : (imageBase + image.replace(/^\/+/, ''))) + '" alt="thumb">' +
            '    </div>' +
            '    <div class="featured-products2-card__content">' +
            '      <div class="featured-products2-card__content-title"><a href="' + detailsBase + slug + '">' + name + '</a></div>' +
            '      <ul class="featured-products2-card__content-list">' +
            '        <li class="featured-products2-card__content-list-start"><i class="fa-solid fa-star fa-fw"></i></li>' +
            '        <li class="featured-products2-card__content-list-point">' + escHtml(rating) + '</li>' +
            '        <li class="featured-products2-card__content-list-text">(' + reviews + ' Reviews)</li>' +
            '      </ul>' +
            '      <div class="featured-products2-card__content-dollar">$' + escHtml(price) + '</div>' +
            '    </div>' +
            '  </div>' +
            '</div>';
        }).join('');
      }

      function initRelatedSlider(sliderEl) {
        if (typeof Swiper === 'undefined') return;

        try {
          if (sliderEl && sliderEl.swiper && typeof sliderEl.swiper.destroy === 'function') {
            sliderEl.swiper.destroy(true, true);
          }
        } catch (error) {}

        new Swiper(sliderEl, {
          loop: true,
          slidesPerView: 1,
          spaceBetween: 20,
          autoplay: {
            delay: 3000,
            disableOnInteraction: false,
          },
          breakpoints: {
            320: { slidesPerView: 1, spaceBetween: 10 },
            640: { slidesPerView: 2, spaceBetween: 10 },
            768: { slidesPerView: 2, spaceBetween: 10 },
            1024: { slidesPerView: 3, spaceBetween: 20 },
            1200: { slidesPerView: 4, spaceBetween: 20 },
          },
          navigation: {
            nextEl: '.featured-products2-controls__arrowRight',
            prevEl: '.featured-products2-controls__arrowLeft',
          },
        });
      }

      document.addEventListener('DOMContentLoaded', function () {
        var section = document.getElementById('related-products-section');
        var wrapper = document.getElementById('related-products-wrapper');
        var loader = document.getElementById('related-products-loader');
        if (!section || !wrapper || !loader) return;

        var slug = String(section.getAttribute('data-product-slug') || '').trim();
        if (!slug) {
          loader.innerHTML = '<span>No featured products available.</span>';
          return;
        }

        var loaded = false;

        function loadRelated() {
          if (loaded) return;
          loaded = true;
          loader.style.display = 'flex';

          var endpoint = '<?php echo htmlspecialchars(url('api/related-products.php'), ENT_QUOTES, 'UTF-8'); ?>?slug=' + encodeURIComponent(slug) + '&limit=12';
          fetch(endpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) { return res.json(); })
            .then(function (json) {
              var items = (json && json.success && json.data && Array.isArray(json.data.items)) ? json.data.items : [];
              renderRelated(items, wrapper);
              loader.style.display = 'none';
              initRelatedSlider(section);
            })
            .catch(function () {
              loader.innerHTML = '<span>Unable to load featured products.</span>';
            });
        }

        if ('IntersectionObserver' in window) {
          var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
              if (entry.isIntersecting) {
                observer.disconnect();
                loadRelated();
              }
            });
          }, { rootMargin: '250px 0px' });

          observer.observe(section);
        } else {
          loadRelated();
        }
      });
    })();
  </script>
  <style>
    .enquiry-modal { position: fixed; inset: 0; z-index: 11000; display: none; align-items: center; justify-content: center; padding: 16px; }
    .enquiry-modal.is-open { display: flex; }
    .enquiry-modal__backdrop { position: absolute; inset: 0; background: rgba(12, 12, 12, 0.56); }
    .enquiry-modal__dialog { position: relative; width: min(560px, 100%); max-height: 90vh; overflow: auto; border-radius: 16px; background: #fff; padding: 22px; box-shadow: 0 18px 36px rgba(0, 0, 0, 0.2); }
    .enquiry-modal__title { margin: 0 0 14px; font-size: 24px; font-weight: 700; color: #0c0c0c; }
    .enquiry-modal__close { position: absolute; top: 8px; right: 12px; border: 0; background: transparent; font-size: 30px; color: #334155; line-height: 1; }
    .enquiry-modal__form { display: grid; gap: 12px; }
    .enquiry-modal__field { display: grid; gap: 6px; font-size: 14px; font-weight: 600; color: #334155; }
    .enquiry-modal__field input, .enquiry-modal__field textarea { border: 1px solid #d0d7de; border-radius: 10px; padding: 10px 12px; font-size: 14px; color: #0f172a; }
    .enquiry-modal__submit { border: 0; border-radius: 999px; padding: 11px 16px; background: #ee2d7a; color: #fff; font-weight: 700; }
    .product-info-btn { min-width: 52px; justify-content: center; }
    .product-info-modal .modal-header { border-bottom: 1px solid #e9ecef; }
    .product-info-modal .modal-title { font-weight: 700; }
    .product-info-tabs .nav-link {
      text-align: left;
      border-radius: 10px;
      font-weight: 600;
      color: #334155;
      margin-bottom: 8px;
      border: 1px solid #e2e8f0;
      background: #fff;
    }
    .product-info-tabs .nav-link.active {
      color: #fff;
      background: #ee2d7a;
      border-color: #ee2d7a;
    }
    .related-products-loader {
      min-height: 180px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      color: #475569;
      font-weight: 600;
    }
    .related-products-loader__spinner {
      width: 24px;
      height: 24px;
      border: 3px solid #e5e7eb;
      border-top-color: #ee2d7a;
      border-radius: 50%;
      animation: relatedSpin 0.8s linear infinite;
    }
    @keyframes relatedSpin {
      to { transform: rotate(360deg); }
    }
    @media (max-width: 767px) {
      .product-info-tabs { flex-direction: row !important; gap: 8px; overflow-x: auto; }
      .product-info-tabs .nav-link { white-space: nowrap; margin-bottom: 0; }
    }
  </style>
</body>

</html>












