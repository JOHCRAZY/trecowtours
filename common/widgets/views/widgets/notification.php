<?php 
/** @var Yii\web\view $this */
?>
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<div id="notification-modal" class="modal fade notification-modal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" data-aos="zoom-in" data-aos-duration="800">
            <div class="modal-body text-center">
                <div class="circle-icon <?= $type ?>-bg" data-aos="fade-down" data-aos-delay="300" data-aos-duration="1000">
                    <?php if ($type == 'info'): ?>
                        <i class="bi bi-info-lg"></i>
                    <?php elseif ($type == 'success'): ?>
                        <i class="bi bi-check-lg"></i>
                    <?php elseif ($type == 'error'): ?>
                        <i class="bi bi-exclamation-lg"></i>
                    <?php elseif ($type == 'warning'): ?>
                        <i class="bi bi-exclamation-triangle"></i>
                    <?php endif; ?>
                </div>
                <h4 class="notification-title" data-aos="fade-up" data-aos-delay="1000"><?= $message ?></h4>
                <?php if (!empty($description)): ?>
                    <p class="notification-description" data-aos="fade-up" data-aos-delay="1300"><?= $description ?></p>
                <?php endif; ?>
                <button class="btn notification-btn <?= $type ?>-btn" data-aos="fade-up" data-aos-delay="1900" data-bs-dismiss="modal">
                    <?= $buttonText ?>
                </button>
            </div>
        </div>
    </div>
</div>


<?php
$this->registerJs("
// Initialize AOS
AOS.init({
    once: true, // Whether animation should happen only once
    mirror: true, // Whether elements should animate out while scrolling past them
    disable: 'mobile' // Disable animations on mobile devices
});

$('#notification-modal').modal('show');

// Reset AOS animations when modal is shown
$('#notification-modal').on('shown.bs.modal', function() {
    setTimeout(function() {
        AOS.refresh();
    }, 100);
});

const notificationSound = new Audio('sound/notification.mp3');
notificationSound.volume = 1;
console.log('Notification sound loaded');
notificationSound.play().catch(error => {
    console.error('Audio playback failed:', error);
});
");
?>
<?php

$css = <<<CSS
    .notification-modal .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        padding: 30px 20px;
        transition: all 0.3s ease;
    }
    
    .notification-modal .circle-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        transition: all 0.3s ease;
    }
    
    .notification-modal .circle-icon i {
        font-size: 32px;
    }
    
    .notification-modal .notification-title {
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }
    
    .notification-modal .notification-description {
        color: #666;
        margin-bottom: 20px;
        font-size: 14px;
    }
    
    .notification-modal .notification-btn {
        padding: 10px 32px;
        font-weight: 500;
        border-radius: 8px;
        border: none;
        margin-top: 10px;
        transition: all 0.2s ease;
    }
    
    .notification-modal .notification-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .notification-modal .info-bg {
        background-color: rgba(0, 208, 255, 0.1);
    }
    
    .notification-modal .info-bg i {
        color: #00d0ff;
    }
    
    .notification-modal .success-bg {
        background-color: rgba(38, 203, 124, 0.1);
    }
    
    .notification-modal .success-bg i {
        color: #26cb7c;
    }
    
    .notification-modal .error-bg {
        background-color: rgba(255, 76, 76, 0.1);
    }
    
    .notification-modal .error-bg i {
        color: #ff4c4c;
    }
    
    .notification-modal .warning-bg {
        background-color: rgba(255, 171, 0, 0.1);
    }
    
    .notification-modal .warning-bg i {
        color: #ffab00;
    }
    
    .notification-modal .info-btn {
        background-color: #00d0ff;
        color: white;
    }
    
    .notification-modal .success-btn {
        background-color: #26cb7c;
        color: white;
    }
    
    .notification-modal .error-btn {
        background-color: #ff4c4c;
        color: white;
    }
    
    .notification-modal .warning-btn {
        background-color: #ffab00;
        color: white;
    }
    
    /* Remove the default pulse animation as we're using AOS instead */
    /* .notification-modal .circle-icon {
        animation: pulse 2s infinite;
    } */
    
    /* Button hover effect with animation */
    .notification-modal .notification-btn:hover {
        animation: pulse 1s;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    /* Ensure AOS animations work within the modal context */
    .modal-open [data-aos] {
        transition-timing-function: cubic-bezier(.25, .8, .25, 1);
    }
CSS;

$this->registerCss($css);

$js = <<<JS
    // Show modal with animation
    $('#notification-modal').modal({
        backdrop: 'static',
        keyboard: false
    });
    
    $('#notification-modal').on('hidden.bs.modal', function () {
        console.log('Notification acknowledged');
    });
    
    // Add entrance animation to specific elements
    $('.notification-btn').addClass('aos-animate');

    setTimeout(function() {
        $('#notification-modal').modal('hide');
    }, 20000); // Close after 20 seconds
    
    // Add subtle hover effect to the button
    $('.notification-btn').hover(
        function() {
            $(this).css('transform', 'translateY(-3px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );
JS;

$this->registerJs($js);
?>