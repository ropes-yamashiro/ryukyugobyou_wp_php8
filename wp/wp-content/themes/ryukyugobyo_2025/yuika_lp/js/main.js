// ===================================
// Navigation Menu Toggle
// ===================================
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    const navLinks = document.querySelectorAll('.nav-link');

    // Toggle mobile menu
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            const icon = navToggle.querySelector('i');
            if (navMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }

    // Close menu when clicking on a link
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                navMenu.classList.remove('active');
                const icon = navToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInsideNav = navMenu.contains(event.target) || navToggle.contains(event.target);
        if (!isClickInsideNav && navMenu.classList.contains('active')) {
            navMenu.classList.remove('active');
            const icon = navToggle.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
});

// ===================================
// Smooth Scroll for Navigation Links
// ===================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const headerOffset = 80;
            const elementPosition = target.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    });
});

// ===================================
// Header Scroll Effect
// ===================================
window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    if (!header) return;
    if (window.scrollY > 50) {
        header.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
    } else {
        header.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.08)';
    }
});

// ===================================
// Scroll to Top Button
// ===================================
const scrollTopBtn = document.getElementById('scrollTop');

if (scrollTopBtn) {
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollTopBtn.classList.add('visible');
        } else {
            scrollTopBtn.classList.remove('visible');
        }
    });

    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ===================================
// Contact Form Handling
// ===================================
const contactForm = document.getElementById('contactForm');

if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = {
            name: document.getElementById('name').value,
            tel: document.getElementById('tel').value,
            email: document.getElementById('email').value,
            interest: document.getElementById('interest').value,
            visit: document.querySelector('input[name="visit"]:checked').value,
            message: document.getElementById('message').value
        };

        // Display success message
        alert('お問い合わせありがとうございます！\n\n担当者より2営業日以内にご連絡させていただきます。\n\nお電話でのお問い合わせも承っております。\n0120-846-940（営業時間：9:30〜17:30 定休日：水曜日）');
        
        // Reset form
        contactForm.reset();
        
        // Log form data to console (for demonstration purposes)
        console.log('Form submitted with data:', formData);
    });
}

// ===================================
// Form Input Validation
// ===================================
const telInput = document.getElementById('tel');
const emailInput = document.getElementById('email');

if (telInput) {
    telInput.addEventListener('blur', function() {
        const telPattern = /^[\d\-+() ]*$/;
        if (this.value && !telPattern.test(this.value)) {
            this.style.borderColor = '#e74c3c';
            showError(this, '有効な電話番号を入力してください');
        } else {
            this.style.borderColor = '';
            removeError(this);
        }
    });
}

if (emailInput) {
    emailInput.addEventListener('blur', function() {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (this.value && !emailPattern.test(this.value)) {
            this.style.borderColor = '#e74c3c';
            showError(this, '有効なメールアドレスを入力してください');
        } else {
            this.style.borderColor = '';
            removeError(this);
        }
    });
}

function showError(element, message) {
    removeError(element);
    const errorMsg = document.createElement('span');
    errorMsg.className = 'error-message';
    errorMsg.style.color = '#e74c3c';
    errorMsg.style.fontSize = '0.875rem';
    errorMsg.style.marginTop = '0.25rem';
    errorMsg.style.display = 'block';
    errorMsg.textContent = message;
    element.parentNode.appendChild(errorMsg);
}

function removeError(element) {
    const errorMsg = element.parentNode.querySelector('.error-message');
    if (errorMsg) {
        errorMsg.remove();
    }
}

// ===================================
// Key Phrase: 3行を順番にスクロールアニメーション発火
// ===================================
document.addEventListener('DOMContentLoaded', function() {
    const phrases = document.querySelectorAll('.key-phrase');
    if (!phrases.length) return;

    let animated = false;

    function triggerAnimation() {
        if (animated) return;
        animated = true;

        phrases.forEach((phrase, i) => {
            // 各行の文字数を取得して、前行の終了後に次行を開始
            // 1行目終了時間 = 文字数 × 0.1s + 0.8s(アニメ時間) ≒ 各行の文字数から算出
            let delay = 0;
            if (i > 0) {
                // 前の行の文字数を数えて、その終了を待つ
                for (let j = 0; j < i; j++) {
                    const charCount = phrases[j].querySelectorAll('.char').length;
                    // charFadeIn = 0.8s, delay_per_char = 0.1s → 最後の文字完了 = charCount * 0.1 + 0.8
                    // 次の行は 0.2s のオーバーラップを入れて自然につなぐ
                    delay += (charCount - 1) * 0.1 + 0.7;
                }
            }
            // 最初の行は少し待ってから開始
            const startDelay = 200 + delay * 1000;
            setTimeout(() => {
                phrase.classList.add('animate');
            }, startDelay);
        });
    }

    // スクロール量チェック: ウィンドウ高さの60%以上スクロールしてから発火
    const minScrollPx = Math.max(window.innerHeight * 0.6, 300);

    // IntersectionObserver: 最初の行が画面内に入ったら発火
    const phraseObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && window.scrollY >= minScrollPx) {
                triggerAnimation();
                phraseObserver.disconnect();
            }
        });
    }, {
        threshold: 0.4,
        rootMargin: '0px 0px 0px 0px'
    });

    phraseObserver.observe(phrases[0]);

    // スクロールイベントフォールバック
    function onScroll() {
        if (animated) {
            window.removeEventListener('scroll', onScroll);
            return;
        }
        if (window.scrollY < minScrollPx) return;

        const rect = phrases[0].getBoundingClientRect();
        const windowHeight = window.innerHeight;
        if (rect.top < windowHeight * 0.9 && rect.bottom > windowHeight * 0.1) {
            triggerAnimation();
            window.removeEventListener('scroll', onScroll);
        }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
});

// ===================================
// Animate Elements on Scroll
// ===================================
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.12,
        rootMargin: '0px 0px -40px 0px'
    };

    // 要素の種類ごとにアニメーション設定を変える
    const animationConfig = {
        'feature-card':  { duration: '0.9s', easing: 'cubic-bezier(0.22, 1, 0.36, 1)', stagger: 0.13 },
        'pricing-card':  { duration: '1.0s', easing: 'cubic-bezier(0.22, 1, 0.36, 1)', stagger: 0.18 },
        'park-feature':  { duration: '0.85s', easing: 'cubic-bezier(0.22, 1, 0.36, 1)', stagger: 0.12 },
        'flow-step':     { duration: '0.9s', easing: 'cubic-bezier(0.22, 1, 0.36, 1)', stagger: 0.15 },
        'support-step':  { duration: '0.85s', easing: 'cubic-bezier(0.22, 1, 0.36, 1)', stagger: 0.12 },
    };

    function getConfig(el) {
        for (const [cls, cfg] of Object.entries(animationConfig)) {
            if (el.classList.contains(cls)) return cfg;
        }
        return { duration: '0.8s', easing: 'cubic-bezier(0.22, 1, 0.36, 1)', stagger: 0.1 };
    }

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const cfg = getConfig(entry.target);
                const delay = parseFloat(entry.target.dataset.delay || 0);
                setTimeout(() => {
                    entry.target.style.animation = `fadeInUp ${cfg.duration} ${cfg.easing} forwards`;
                    entry.target.style.opacity = '1';
                }, delay * 1000);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // 各グループ内で同じ種類のカードをグループ化してstaggerを計算
    const groups = {};
    const allElements = document.querySelectorAll(
        '.feature-card, .pricing-card, .support-step, .park-feature, .flow-step'
    );

    allElements.forEach((el) => {
        // 親要素をキーにしてグループ化
        const parentKey = el.parentElement ? el.parentElement.className : 'default';
        if (!groups[parentKey]) groups[parentKey] = [];
        groups[parentKey].push(el);
    });

    // グループ内のインデックスに基づいてstaggerを設定
    allElements.forEach((el) => {
        const cfg = getConfig(el);
        const parentKey = el.parentElement ? el.parentElement.className : 'default';
        const group = groups[parentKey];
        const indexInGroup = group ? group.indexOf(el) : 0;
        el.style.opacity = '0';
        el.dataset.delay = indexInGroup * cfg.stagger;
        observer.observe(el);
    });
});

// ===================================
// Active Navigation Link Highlighting
// ===================================
window.addEventListener('scroll', function() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    
    let current = '';
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (window.pageYOffset >= sectionTop - 100) {
            current = section.getAttribute('id');
        }
    });
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('active');
            link.style.color = 'var(--primary-green)';
        } else {
            link.style.color = '';
        }
    });
});

// ===================================
// Pricing Card Interactions
// ===================================
const pricingCards = document.querySelectorAll('.pricing-card');
pricingCards.forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-12px) scale(1.02)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = '';
    });
});

// ===================================
// Feature Card Interactions
// ===================================
const featureCards = document.querySelectorAll('.feature-card');
featureCards.forEach(card => {
    card.addEventListener('mouseenter', function() {
        const icon = this.querySelector('.feature-icon');
        if (icon) {
            icon.style.transform = 'scale(1.1) rotate(5deg)';
            icon.style.transition = 'transform 0.3s ease';
        }
    });
    
    card.addEventListener('mouseleave', function() {
        const icon = this.querySelector('.feature-icon');
        if (icon) {
            icon.style.transform = '';
        }
    });
});

// ===================================
// Handle Window Resize
// ===================================
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        // Close mobile menu on resize to desktop
        if (window.innerWidth > 768) {
            const navMenu = document.getElementById('navMenu');
            const navToggle = document.getElementById('navToggle');
            if (navMenu && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                const icon = navToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    }, 250);
});

// ===================================
// Scroll Progress Indicator (Optional)
// ===================================
function updateScrollProgress() {
    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (winScroll / height) * 100;
    
    // You can add a progress bar element to show scroll progress
    // For now, just log it
    // console.log('Scroll progress:', scrolled + '%');
}

window.addEventListener('scroll', updateScrollProgress);

// ===================================
// Lazy Loading for Placeholder Images (when real images are added)
// ===================================
document.addEventListener('DOMContentLoaded', function() {
    const placeholders = document.querySelectorAll('.hero, .circle-placeholder, .pricing-placeholder, .feature-image-placeholder, .access-map-placeholder, .qr-placeholder');
    
    // Add subtle animation to placeholders
    placeholders.forEach(placeholder => {
        placeholder.style.transition = 'all 0.3s ease';
    });
});

// ===================================
// Phone Number Click Tracking (for analytics)
// ===================================
const phoneLinks = document.querySelectorAll('a[href^="tel:"]');
phoneLinks.forEach(link => {
    link.addEventListener('click', function() {
        console.log('Phone call initiated:', this.getAttribute('href'));
        // Here you can add analytics tracking code
    });
});

// ===================================
// Form Field Auto-formatting
// ===================================
const telInputField = document.getElementById('tel');
if (telInputField) {
    telInputField.addEventListener('input', function(e) {
        // Remove non-numeric characters except + and -
        let value = e.target.value.replace(/[^\d\-+]/g, '');
        e.target.value = value;
    });
}

// ===================================
// Smooth Reveal for Hero Section
// ===================================
document.addEventListener('DOMContentLoaded', function() {
    const heroContent = document.querySelector('.hero-content');
    if (heroContent) {
        setTimeout(() => {
            heroContent.style.opacity = '1';
        }, 100);
    }
});

// ===================================
// Add Floating Animation to Hero Elements
// ===================================
document.addEventListener('DOMContentLoaded', function() {
    const qrPlaceholder = document.querySelector('.qr-placeholder');
    if (qrPlaceholder) {
        qrPlaceholder.style.animation = 'float 3s ease-in-out infinite';
    }
});

// ===================================
// Accordion Functionality
// ===================================
document.addEventListener('DOMContentLoaded', function() {
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    
    accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const accordionItem = this.parentElement;
            const isActive = accordionItem.classList.contains('active');
            
            // Close all accordion items
            document.querySelectorAll('.accordion-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Open clicked item if it wasn't active
            if (!isActive) {
                accordionItem.classList.add('active');
            }
        });
    });
});

// ===================================
// Console Welcome Message
// ===================================
console.log('%c🌸 樹木葬 結花（ゆいか）', 'color: #2d7d46; font-size: 24px; font-weight: bold;');
console.log('%c沖縄初のサークル型樹木葬', 'color: #4a90a4; font-size: 14px;');
console.log('%c家族の絆を紡ぐ、新しい安らぎのカタチ', 'color: #5a6c7d; font-size: 12px;');
console.log('%cお問い合わせ: 0120-846-940', 'color: #2d7d46; font-size: 12px; font-weight: bold;');