<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurryTales - Premium Pet Boutique</title>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet"></script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<script>
    document.addEventListener("DOMContentLoaded", function () {

    const toggle = document.getElementById("menuToggle");
    const navLinks = document.getElementById("navLinks");

    toggle.addEventListener("click", function () {
        navLinks.classList.toggle("active");
        toggle.classList.toggle("active");
    });

});
</script>
<body class="flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <!-- Logo -->
            <a href="<?php echo e(route('client.home')); ?>" class="navbar-logo">
                <h2 class="navbar-logo-text">
                    FurryTales
                </h2>
            </a>

            <!-- Navigation Links -->
            <div class="navbar-links" id="navLinks">
                <a href="<?php echo e(route('client.home')); ?>" class="navbar-link">Home</a>
                <a href="<?php echo e(route('client.pets.index')); ?>" class="navbar-link">Pets</a>
                <a href="<?php echo e(route('client.accessories.index')); ?>" class="navbar-link">Accessories</a>
                <a href="<?php echo e(route('client.orders.index')); ?>" class="navbar-link">My Orders</a>
                <a href="<?php echo e(route('client.appointments.index')); ?>" class="navbar-link">Appointments</a>
                <a href="<?php echo e(route('client.help.show')); ?>" class="navbar-link">Help</a>
                <a href="<?php echo e(route('client.reviews.index')); ?>" class="navbar-link">Reviews</a>
            </div>

            <!-- Hamburger -->
            <button class="navbar-toggle" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <!-- User Actions -->
            <div class="navbar-actions">
                <a href="<?php echo e(route('client.cart.view')); ?>" class="navbar-icon">
                    <img src="<?php echo e(asset('image/cart.png')); ?>" alt="Cart">
                </a>
                
                <a href="<?php echo e(route('client.profile.view')); ?>" class="block">
                    <?php
                        $profilePhoto = asset('image/profile.png');
                        if (session('customer_id')) {
                            $customer = \App\Models\Customer::where('customerID', session('customer_id'))->first();
                            if ($customer && $customer->profile_photo) {
                                $profilePhoto = asset('image/' . $customer->profile_photo);
                            }
                        }
                    ?>
                    <img class="profile-img" src="<?php echo e($profilePhoto); ?>" alt="Profile">
                </a>

                <a href="<?php echo e(route('client.login.page')); ?>" class="navbar-icon">
                    <img src="<?php echo e(asset('image/logout.png')); ?>" alt="Logout">
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-grid">
                <!-- About Column -->
                <div class="footer-section">
                    <h4>Our Story</h4>
                    <p>
                        Connecting loving homes with ethically sourced, pedigree pets. We believe every animal deserves a life filled with love and luxury.
                    </p>
                </div>

                <!-- Navigation Column -->
                <div class="footer-section">
                    <h4>Explore</h4>
                    <ul>
                        <li><a href="<?php echo e(route('client.home')); ?>">Home</a></li>
                        <li><a href="<?php echo e(route('client.pets.index')); ?>">Browse Pets</a></li>
                        <li><a href="<?php echo e(route('client.orders.index')); ?>">My Orders</a></li>
                        <li><a href="<?php echo e(route('client.appointments.index')); ?>">Appointments</a></li>
                    </ul>
                </div>

                <!-- Contact Column -->
                <div class="footer-section">
                    <h4>Contact</h4>
                    <div class="footer-contact-item">
                        <h5>Location</h5>
                        <p>38-A, Jalan SS 22/25, Damansara Jaya, 47400 Petaling Jaya, Selangor</p>
                    </div>
                    <div class="footer-contact-item">
                        <h5>Phone</h5>
                        <p>+603 4904 255</p>
                    </div>
                    <div class="footer-contact-item">
                        <h5>Email</h5>
                        <p>furrytales@gmail.com</p>
                    </div>
                </div>

                <!-- Map Column -->
                <div class="footer-section">
                    <h4>Find Us</h4>
                    <div class="footer-map">
                        <img src="<?php echo e(asset('image/map.png')); ?>" alt="Map">
                    </div>
                </div>
            </div>

            <div class="footer-copyright">
                <small>© <?php echo e(date('Y')); ?> FurryTales. All rights reserved.</small>
            </div>
        </div>
    </footer>

</body>
</html><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/layouts/app.blade.php ENDPATH**/ ?>