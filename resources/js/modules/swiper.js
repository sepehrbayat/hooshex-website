import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

/**
 * Initialize all Swiper sliders on the page
 */
export function initSwipers() {
    // Testimonials Slider - Mobile: 2 cards per slide, Desktop: 3 cards
    const testimonialsSlider = document.querySelector('.testimonials-swiper');
    if (testimonialsSlider) {
        new Swiper(testimonialsSlider, {
            modules: [Navigation, Pagination],
            slidesPerView: 1,
            spaceBetween: 16,
            breakpoints: {
                430: {
                    slidesPerView: 1.5,
                    spaceBetween: 16,
                },
                640: {
                    slidesPerView: 2,
                    spaceBetween: 16,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 29,
                },
            },
            navigation: {
                nextEl: '.testimonials-swiper-button-prev', // Swapped for RTL
                prevEl: '.testimonials-swiper-button-next', // Swapped for RTL
            },
            pagination: {
                el: '.testimonials-swiper-pagination',
                clickable: true,
            },
            dir: 'rtl',
            rtl: true,
        });
    }

    // Popular Courses Slider
    const coursesSlider = document.querySelector('.courses-swiper');
    if (coursesSlider) {
        new Swiper(coursesSlider, {
            modules: [Navigation, Pagination],
            slidesPerView: 1,
            spaceBetween: 16,
            breakpoints: {
                430: {
                    slidesPerView: 1,
                    spaceBetween: 16,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 2,
                    spaceBetween: 24,
                },
                1280: {
                    slidesPerView: 3,
                    spaceBetween: 24,
                },
            },
            navigation: {
                nextEl: '.courses-swiper-button-prev', // Swapped for RTL
                prevEl: '.courses-swiper-button-next', // Swapped for RTL
            },
            pagination: {
                el: '.courses-swiper-pagination',
                clickable: true,
            },
            dir: 'rtl',
            rtl: true,
        });
    }

    // Blog Slider
    const blogSlider = document.querySelector('.blog-swiper');
    if (blogSlider) {
        new Swiper(blogSlider, {
            modules: [Navigation, Pagination],
            slidesPerView: 1,
            spaceBetween: 16,
            breakpoints: {
                430: {
                    slidesPerView: 1,
                    spaceBetween: 16,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 16,
                },
                1024: {
                    slidesPerView: 4,
                    spaceBetween: 16,
                },
            },
            navigation: {
                nextEl: '.blog-swiper-button-prev', // Swapped for RTL
                prevEl: '.blog-swiper-button-next', // Swapped for RTL
            },
            pagination: {
                el: '.blog-swiper-pagination',
                clickable: true,
            },
            dir: 'rtl',
            rtl: true,
        });
    }
}

