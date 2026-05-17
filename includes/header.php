<?php
if (!isset($pageTitle)) {
    $pageTitle = "Corporate Giveaways | CreativeKit3A";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Customized Silicone Wristbands &amp; Baller IDs Philippines | Pinoyballers">
  <title><?php echo $pageTitle; ?></title>


  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">

  <!-- YOUR STYLESHEET -->
  <link rel="stylesheet" href="/CREATIVEKIT3A-WEBSITE/style.css">
  
</head>

<body>

  
  <!-- ============================================================
       TOP BAR
       ============================================================ -->
       
  <div class="top-bar">
    <div class="container">
      <ul class="top-bar-social">
        <li><a href="https://www.facebook.com/profile.php?id=100063787431490" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a></li>
       <!-- Auth buttons — CHANGED: now open SEPARATE modals (login-modal / register-modal) -->
        <li class="top-bar-auth">
          <button class="btn btn-outline btn-sm" onclick="openModal('login-modal')">Sign In</button>
          <!-- CHANGED: Register now opens its own dedicated modal -->
          <button class="btn btn-primary btn-sm" onclick="openModal('register-modal');">Register</button>
        </li>

      </ul>
      <div class="top-bar-contact">
        <a href="mailto:salesandmarketing@creativekit3a.com"><i class="fas fa-envelope icon"></i> salesandmarketing@creativekit3a.com
</a>
        <a href="tel:+639177142774"><i class="fas fa-mobile-alt icon"></i> +63 917 714 2774</a>
        <a href="tel:+639339927410"><i class="fas fa-phone icon"></i> +63 933 992 7410</a>
      </div>
    </div>
  </div>

  <!-- ============================================================
       MAIN NAVBAR
       ============================================================ -->
  <nav class="navbar" role="navigation" aria-label="Main navigation">
    <div class="navbar-inner">

      <!-- Hamburger (mobile) -->
      <button class="hamburger" aria-label="Open menu" aria-expanded="false" aria-controls="sidenav">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <!-- Logo -->
      <!-- AFTER -->
<a href="/" class="navbar-logo">
  <img src="2.png" alt="CreativeKit3A Logo" class="logo-img">
  <span class="logo-text">CreativeKit3A</span>
</a>

      <!-- Search -->
      <div class="navbar-search" role="search">
        <input type="search" placeholder="What are you looking for?" aria-label="Search products">
        <i class="fas fa-search search-icon" aria-hidden="true"></i>
      </div>

      <!-- Right side -->
      <div class="navbar-right">
        <nav class="navbar-links" aria-label="Secondary navigation">
          <a href="/about/">About</a>
          <a href="/accessories-gadgets/index2.html">Category</a>
          <a href="/contact-us/">Contact Us</a>
        </nav>
        <button class="btn-chat" aria-label="Request a Quote">Request a Quote</button>
        
        <a href="/cart/" class="navbar-cart" aria-label="Shopping cart">
          <i class="fas fa-shopping-cart" aria-hidden="true"></i>
          <span>Cart</span>
          <span class="cart-count" aria-live="polite">0</span>
        </a>

      </div>

    </div>
  </nav>

  <!-- ============================================================
       MEGA MENU BAR
       ============================================================ -->
  <div class="mega-menu-bar" role="navigation" aria-label="Category navigation">
    <ul class="mega-menu-nav">

      <li class="mega-menu-item">
        <a href="category-page.html">ACCESSORIES & GADGETS <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
        <div class="mega-dropdown">
          <div class="mega-dropdown-col">
            <h4>Car Accessories</h4>
            <a href="/product/car-charger-adapters.html">Car Charger Adapters</a>
            <a href="/product/car-organizers.html">Car Organizers & Holders</a>
            <a href="/product/car-dashcams">Dash Cams</a>
          
          </div>
          <div class="mega-dropdown-col">
            <h4>Computer Accessories</h4>
            <a href="/product/computer-audio">Audio Accessories</a>
            <a href="/product/computer-laptop-keyboard">Laptop & Keyboard Accessories</a>

          </div>
          <div class="mega-dropdown-col">
            <h4>Desk Accessories</h4>
            <a href="/product/desk-clocks">Clocks & Timepieces</a>
            <a href="/product/desk-organizers">Desk Organizers & Pen Holders</a>
          </div>
          <div class="mega-dropdown-col">
            <h4>Mobile Accessories</h4>
            <a href="/product/mobile-powerbanks">Mobile Power Banks</a>
            <a href="/product/mobile-stands">Mobile Stands</a>

          </div>
          <div class="mega-dropdown-col">
            <h4>Gadgets</h4>
            <a href="/product/gadgets-charging-devices">Charging Devices</a>
            <a href="/product/gadgets-portable-fans">Portable Fans</a>
            <a href="/product/gadgets-powerbanks">Power Banks</a>

          </div>
        </div>
      </li>

      <li class="mega-menu-item">
        <a href="/product-category/apparel">APPAREL <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
        <div class="mega-dropdown">
          <div class="mega-dropdown-col">
            <h4>Caps</h4>
            <a href="/product/cap-baseball/">Baseball Cap</a>
            <a href="/product/cap-bucket-hat/">Bucket Hat</a>
          </div>
          <div class="mega-dropdown-col">
            <h4>Jackets</h4>
            <a href="/product/jackets-corporate/">Corporate Jackets</a>
            <a href="/product/jacket-hoodies/">Hoodies </a>
          </div>
          <div class="mega-dropdown-col">
            <h4>Shirts</h4>
            <a href="/product/shirt-collared/">Collared Shirt</a>
            <a href="/product/shirt-dri-fit/">Dri Fit Shirt</a>
          </div>
        </div>
      </li>

      <li class="mega-menu-item">
        <a href="/product-category/bags-pouches">BAGS & POUCHES<i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
        <div class="mega-dropdown">
          <div class="mega-dropdown-col">
            <h4>Backpacks</h4>
            <a href="/product/backpacks-sports/">Sports Backpacks</a>
            <a href="/product/backpacks-travel/">Travel Backpacks</a>
          </div>
          <div class="mega-dropdown-col">
            <h4>Eco Bags</h4>
            <a href="/product/ecobags-shopping-tote/">Foldable Shopping Totes</a>
            <a href="/product/ecobags-non-woven/">Non Woven Shopping Bags</a>
          </div>
          <div class="mega-dropdown-col">
            <h4>Foldable Bags</h4>
            <a href="/product/foldable-backpack/">Foldable Backpacks</a>
            <a href="/product/foldable-tote-bag/">Foldable Tote Bags</a>
          </div>
          <div class="mega-dropdown-col">
            <h4>Laptop Bags</h4>
            <a href="/product/laptop-backpack/">Laptop Backpacks</a>
            <a href="/product/laptop-hand-carry-bag/">Laptop Hand Carry Bags</a>
          </div>
          <div class="mega-dropdown-col">
            <h4>Lunch Bags</h4>
            <a href="/product/lunch-bag-hand-carry/">Hand Carry Lunch Bags</a>
            <a href="/product/lunch-bag-tote-bag/">Tote Lunch Bags</a>
          </div>
          <div class="mega-dropdown-col">
            <h4>Organizer Pouches</h4>
            <a href="/product/organizer-cable-pouche/">Cable Organizer Pouches</a>
            <a href="/product/organizer-clutch-pouche/">Clutch Pouches</a>
          </div>
          <div class="mega-dropdown-col">
            <h4>Travel Bags</h4>
            <a href="/product/travel-bag-duffel/">Duffel Travel Bags</a>
            <a href="/product/travel-bag-tote/">Tote Travel Bag</a>
          </div>
          <div class="mega-dropdown-col">
            <h4>Tote Bags</h4>
            <a href="/product/tote-bag-classic-zip/">Classic Zip Totes</a>
            <a href="/product/tote-bag-foldable-zip/">Foldable Totes</a>
          </div>
        </div>
      </li>

      <li class="mega-menu-item">
        <a href="/product-category/drinkware">DRINKWARE <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
        <div class="mega-dropdown">
          <div class="mega-dropdown-col">
            <h4>Tumbler & Flasks</h4>
            <a href="/product/tumbler-bamboo/">Bamboo Tumblers </a>
            <a href="/product/tumbler-tumblers/">Tumblers </a>
             <a href="/product/tumbler-stainless/">Stainless Steel Tumblers</a>
          </div>
          <div class="mega-dropdown-col">
            <h4>Mugs & Cups</h4>
            <a href="/product/mugs-ceramic/">Ceramic Mugs</a>
            <a href="/product/mugs-coffee/">Coffee Mugs</a>
            <a href="/product/mugs-glass/">Glass Mugs</a>
            <a href="/product/mugs-stainless/">Stainless Steel Mugs</a>
          </div>
          <div class="mega-dropdown-col">
            <h4>Water Bottles</h4>
            <a href="/product/water-bottle-collapsible/">Collapsible Bottles</a>
            <a href="/product/water-bottle-sports/">Sports Bottles</a>
          </div>
          <div class="mega-dropdown-col">
            <h4>Eco-Friendly Options</h4>
            <a href="/product/eco-friendly-bamboo/">Bamboo Drinkware</a>
          </div>
        </div>
      </li>

      <li class="mega-menu-item">
        <a href="/product-category/giftset">GIFT SET  <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
         <div class="mega-dropdown">
          <div class="mega-dropdown-col">
            <h4>Gift Set</h4>
            <a href="/product/gift-set-desk-essentials/">Desk Essentials Gift Sets</a>
            <a href="/product/gift-set-employee-onboarding/">Employee Onboarding Sets </a>
            <a href="/product/gift-set-health-wellness/">Health & Wellness Gift Sets</a>
            <a href="/product/gift-set-travel-executive/">Travel Executive Gift Sets </a>
            <a href="/product/gift-set-work-from-home/">Work From Home Gift Sets</a>
          </div>
      </li>

      <li class="mega-menu-item">
        <a href="/product-category/home-living">HOME LIVING <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
        <div class="mega-dropdown">
          <div class="mega-dropdown-col">
            <h4>Home Living</h4>
            <a href="/product/home-living-cleaning-essentials/">Cleaning Essentials </a>
            <a href="/product/home-living-cutlery-sets/">Cutlery Sets</a>
            <a href="/product/home-living-gourmet/">Gourmet </a>
            <a href="/product/home-living-personal-care/">Personal Care</a>
            <a href="/product/home-living-pillows/">Pillows</a>
          </div>
      </li>
      <li class="mega-menu-item">
        <a href="/product-category/pen-paper/">PEN & PAPER <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
        <div class="mega-dropdown">
            <div class="mega-dropdown-col">
            <h4>Pen</h4>
            <a href="/product/pen-bamboo/">Bamboo Pens</a>
            <a href="/product/pen-metal/">Metal Pens </a>
            <a href="/product/pen-multi-functional/">Multi-Functional Pens</a>
            <a href="/product/pen-sign/">Sign Pens</a>
          </div>
           <div class="mega-dropdown-col">
            <h4>Paper</h4>

            <a href="/product/paper-desk-calendars/">Custom Desk Calendars </a>
            <a href="/product/paper-notebooks-journals">Custom Notebooks & Journals</a>
            <a href="/product/paper-office-stationery/">Office Stationery </a>
            <a href="/product/paper-padfolios">Padfolios</a>
            <a href="/product/paper-wired-notebooks/">Wired Notebooks</a>
          </div>
      </li>

       <li class="mega-menu-item">
        <a href="/product-category-pu-leather/">PU LEATHER<i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
        <div class="mega-dropdown">
          <div class="mega-dropdown-col">
            <h4>PU Leather</h4>
            <a href="/product/pu-leather-card-cases/">Card Cases</a>
            <a href="/product/pu-leather-desk-mats/">Desk Mats</a>
            <a href="/product/pu-leather-pouches/">Leather Pouches</a>
            <a href="/product/pu-leather-keychains/">Keychains</a>
            <a href="/product/pu-leather-luggage-tags/">Luggage Tags</a>
            <a href="/product/pu-leather-organizers/">Organizers</a>
            <a href="/product/pu-leather-wallets/">Wallets</a>
          </div>
      </li>

    </ul>
  </div>

  <!-- Mobile Sidenav Overlay -->
  <div class="sidenav-overlay" aria-hidden="true"></div>

  <!-- Mobile Sidenav -->
  <aside class="sidenav" id="sidenav" role="complementary" aria-label="Mobile navigation">
    <div class="sidenav-header">
      <span class="logo-text">CreativeKit3A</span>
      <button class="sidenav-close" aria-label="Close menu">&times;</button>
    </div>
    <div class="sidenav-body">
      <p class="sidenav-section-title">Category</p>
      <a href="/product-category/accessories-gadgets">ACCESSORIES & GADGETS</a>
      <a href="/product-category/apparel">APPAREL</a>
      <a href="/product-category/bags-pouches">BAGS & POUCHES</a>
      <a href="/product-category/">DRINKWARE</a>
      <a href="/custom-drinkware">GIFT SET</a>
      <a href="/custom-clothing-and-apparel">HOME LIVING</a>
      <a href="/custom-office-supplies">PEN & PAPER</a>
      <a href="/corporate-giveaways">PU LEATHER</a>
      <p class="sidenav-section-title">Help</p>
      <a href="/faq/">About</a>
      <a href="/terms-conditions/">Category</a>
      <a href="/contact-us/">Contact Us</a>
      <!--
     
      <a href="/about-us/">About Us</a>
      -->
      <p class="sidenav-section-title">Contact</p>
      <a href="tel:+639177142774"><i class="fas fa-mobile-alt"></i> +63 917 714 2774</a>
      <a href="tel:+639339927410"><i class="fas fa-phone"></i> +63 933 992 7410</a>
      <a href="mailto:salesandmarketing@creativekit3a.com"><i class="fas fa-envelope"></i> salesandmarketing@creativekit3a.com</a>
    </div>
  </aside>



