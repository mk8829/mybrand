<?php
$meta = [
  'title' => 'Mybrandplease | contact',
  'description' => 'Mybrandplease - contact page',
  'canonical' => 'contact.php'
];
include 'includes/head.php';
include 'includes/header.php';
?>

<div class="breadcumb">
          <div class="container rr-container-1895">
            <div class="breadcumb-wrapper section-spacing-120 fix" data-bg-src="assets/imgs/breadcumbBg.jpg">
              <div class="breadcumb-wrapper__title">Contact Us</div>
              <ul class="breadcumb-wrapper__items">
                <li class="breadcumb-wrapper__items-list">
                  <i class="fa-regular fa-house"></i>
                </li>
                <li class="breadcumb-wrapper__items-list">
                  <i class="fa-regular fa-chevron-right"></i>
                </li>
                <li class="breadcumb-wrapper__items-list">
                  <a href="contact.php" class="breadcumb-wrapper__items-list-title">
                    Category
                  </a>
                </li>
                <li class="breadcumb-wrapper__items-list">
                  <i class="fa-regular fa-chevron-right"></i>
                </li>
                <li class="breadcumb-wrapper__items-list">
                  <a href="contact.php" class="breadcumb-wrapper__items-list-title2">
                    Contact Us
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>


        <section class="contact2 section-spacing-120 rr-ov-hidden">
          <div class="container">
            <div class="row d-flex justify-content-center">
              <div class="col-xl-7">
                <div class="section-heading wow fadeInRight" data-wow-delay="0.3s">
                  <h2 class="section-heading__title">Get In Touch Today!</h2>
                  <p class="section-heading__text">We’d love to hear from you! Reach out today for inquiries, support,
                    or collaborations, and our friendly team will respond promptly with all the help you need.</p>
                </div>
                <form action="contact.php" id="contact-form" method="POST" class="contact2-form">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <div class="contact2-form__content">
                    <div class="row g-4">
                      <div class="col-lg-6 wow fadeInUp" data-wow-delay=".3s">
                        <div class="contact2-form__input">
                          <span class="contact2-form__input-name">Your Name</span>
                          <input type="text" class="contact2-form__input-field" name="name" id="name"
                            placeholder="Your name">
                        </div>
                      </div>
                      <div class="col-lg-6 wow fadeInUp" data-wow-delay=".5s">
                        <div class="contact2-form__input">
                          <span class="contact2-form__input-name">Your Email</span>
                          <input type="text" class="contact2-form__input-field" name="email" id="email1"
                            placeholder="Email address">
                        </div>
                      </div>
                      <div class="col-lg-12 wow fadeInUp" data-wow-delay=".7s">
                        <div class="contact2-form__input">
                          <span class="contact2-form__input-name">Your Message</span>
                          <textarea name="message" class="contact2-form__input-field textarea" id="message"
                            placeholder="Type your message"></textarea>
                        </div>
                      </div>
                      <div class="col-lg-12 wow fadeInUp" data-wow-delay=".9s">
                        <div class="contact2-form__button">
                          <a class="btn-orange" href="contact.php">SENDMESSAGES</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </section>


        <div class="map fix">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d52872517.59607392!2d-161.691169406869!3d36.018281840171966!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x54eab584e432360b%3A0x1c3bb99243deb742!2sUnited%20States!5e0!3m2!1sen!2sbd!4v1769883541208!5m2!1sen!2sbd"></iframe>
        </div>

<?php include 'includes/footer.php'; ?>

