document.addEventListener('DOMContentLoaded', function() {
    // Section Appear Animation Observer
    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('section-visible');
                entry.target.classList.remove('section-hidden');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Observe all sections for appear animations
    const animatedSections = document.querySelectorAll('section, .hero-section, .intro-section');
    animatedSections.forEach(section => {
        section.classList.add('section-hidden');
        sectionObserver.observe(section);
    });

    // Staggered animation for child elements
    const staggerObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const children = entry.target.querySelectorAll('.stagger-item');
                children.forEach((child, index) => {
                    let delay = 0;
                    // Mascot appears first (index 0)
                    if (child.classList.contains('hero-mascot')) {
                        delay = 0;
                    }
                    // Text boxes appear after mascot with staggered delay
                    else {
                        delay = 300 + (index * 200); // 300ms after mascot, then 200ms between text boxes
                    }

                    setTimeout(() => {
                        child.classList.add('stagger-visible');
                    }, delay);
                });
            }
        });
    }, {
        threshold: 0.1
    });

    // Observe elements with staggered children
    const staggerContainers = document.querySelectorAll('.stagger-container');
    staggerContainers.forEach(container => {
        staggerObserver.observe(container);
    });
});
