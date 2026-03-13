

<?php $__env->startSection('content'); ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <!-- Text Content -->
            <div class="hero-text">
                <h1>
                    Every Pet Has <br/><span class="gradient-text">A Story</span>
                </h1>
                <p>
                    We bring you adorable, well-cared-for pets from ethical breeders and shelters—ready to be part of your home.
                </p>
                <a href="<?php echo e(route('client.pets.index')); ?>" class="hero-button">
                    Browse Pets
                </a>
            </div>
            <!-- Image Hero -->
            <div class="hero-image">
                <img src="<?php echo e(asset('image/home1.jpg')); ?>" alt="Pets">
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section">
    <div class="container">
        <div class="about-content">
            <h3>Behind the Paws</h3>
            <div class="about-text">
                <p>
                    At FurryTales, we believe that purebred pets deserve homes as exceptional as they are. 
                    Founded by passionate animal lovers, our boutique pet shop specializes in connecting discerning 
                    owners with ethically sourced, high-quality purebred dogs, cats, and more.
                </p>
                <p>
                    Each animal is carefully selected for pedigree, health, and temperament—ensuring that your new 
                    companion is not only beautiful but also well-adjusted and loving. From exclusive breeder partnerships 
                    to personalized support, we make the journey of pet ownership refined, safe, and joyful.
                </p>
                <p class="quote">
                    "This isn't just a store—it's a trusted gateway to a lifelong bond."
                </p>
            </div>
        </div>
        
        <!-- Quick Contact Ribbon -->
        <div class="contact-ribbon">
            <div class="contact-ribbon-item">
                <span>📞</span>
                <span>+003-399 9999</span>
            </div>
            <div class="contact-divider"></div>
            <div class="contact-ribbon-item">
                <span>✉️</span>
                <span>furryTales@gmail.com</span>
            </div>
        </div>
    </div>
</section>

<!-- Founder Section -->
<section class="founder-section">
    <div class="container">
        <h3>About the Founder</h3>
        
        <div class="founder-card">
            <div class="founder-image-wrapper">
                <img src="<?php echo e(asset('image/founder.jpg')); ?>" alt="Founder">
            </div>
            
            <div class="founder-info">
                <div class="founder-header">
                    <h4>Amelia Wong</h4>
                    <p>Founder & Pet Specialist</p>
                </div>

                <div class="founder-details">
                    <div class="founder-detail-item">
                        <span>Age:</span>
                        <span>34</span>
                    </div>
                    <div class="founder-detail-item">
                        <span>Location:</span>
                        <span>Petaling Jaya, Malaysia</span>
                    </div>
                    <div class="founder-detail-item founder-detail-full">
                        <span>Experience:</span>
                        <span>12 years in the pet industry</span>
                    </div>
                    <div class="founder-detail-item founder-detail-full">
                        <span>Passion:</span>
                        <span>Connecting pets with loving, responsible owners</span>
                    </div>
                    <div class="founder-detail-item founder-detail-full">
                        <span>Fav Pet:</span>
                        <span>British Shorthair named <em>Duchess</em></span>
                    </div>
                </div>

                <blockquote class="founder-quote">
                    "Purebred pets deserve homes that match their beauty, loyalty, and charm."
                </blockquote>
            </div>
        </div>
    </div>
</section>

<!-- Featured Pets Preview -->
<section class="featured-section">
    <div class="container">
        <div class="featured-header">
            <h3>New Arrivals</h3>
            <a href="<?php echo e(route('client.pets.index')); ?>">View All Pets &rarr;</a>
        </div>

        <div class="featured-grid">
            <!-- Card 1 -->
            <div class="featured-card">
                <div class="featured-card-image">
                    <img src="<?php echo e(asset('image/dog1.jpeg')); ?>" alt="dog">
                </div>
                <div class="featured-card-content">
                    <h4>Pets</h4>
                    <a href="<?php echo e(route('client.pets.index')); ?>" class="featured-card-button">
                        Take Me Home!
                    </a>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="featured-card">
                <div class="featured-card-image">
                    <img src="<?php echo e(asset('image/appointment.jpg')); ?>" alt="cat">
                </div>
                <div class="featured-card-content">
                    <h4>Viewing Appointments</h4>
                    <a href="<?php echo e(route('client.appointments.index')); ?>" class="featured-card-button">
                        Book Now!
                    </a>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="featured-card">
                <div class="featured-card-image">
                    <img src="<?php echo e(asset('image/accessory.webp')); ?>" alt="dog">
                </div>
                <div class="featured-card-content">
                    <h4>Pets Accessories</h4>
                    <a href="<?php echo e(route('client.accessories.index')); ?>" class="featured-card-button">
                        Buy One!
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const loggedInUserID = "<?php echo e(Auth::check() && Auth::user()->customer ? Auth::user()->customer->customerID : ''); ?>";
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\finalyear\resources\views/client/home.blade.php ENDPATH**/ ?>