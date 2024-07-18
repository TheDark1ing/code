/*
Формирование slick slider с таймером. Слайд проигрывается пока не закончится видео. Для изображений таймер 5 секунд.
*/

$(function () {
    let heroSlider = $(".hero__carousel").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        dots: true,
        autoplay: false,
        speed: 800,
        arrows: false,
        pauseOnHover: false,
    });

    let timer; // Переменная для хранения идентификатора таймера

    function checkSlickInitialized() {
        if ($('.hero__carousel').hasClass('slick-initialized')) {
            startSlideShow(heroSlider.slick('getSlick'));
        } else {
            setTimeout(checkSlickInitialized, 500);
        }
    }

    checkSlickInitialized();

    $('.hero__carousel').on('beforeChange', function(event, slick, currentSlide, nextSlide) {
        stopCurrentVideo(slick, currentSlide);
        // Очищаем таймер перед сменой слайда
        clearTimeout(timer);
    });

    $('.hero__carousel').on('afterChange', function(event, slick, currentSlide) {
        startSlideShow(slick);
    });

    function startSlideShow(slick) {
        var currentSlide = slick.$slides.get(slick.currentSlide);
        var $video = $(currentSlide).find('video');
        var $image = $(currentSlide).find('img');

        if ($video.length) {
            $video.off('ended');
            $video[0].currentTime = 0;
            $video[0].play();
            $video.on('ended', function() {
                slick.slickNext();
            });
        } else if ($image.length) {
            // Устанавливаем таймер на 5 секунд
            timer = setTimeout(function() {
                slick.slickNext();
            }, 5000);
        }
    }

    function stopCurrentVideo(slick, currentSlide) {
        var currentSlideElem = slick.$slides.get(currentSlide);
        var $video = $(currentSlideElem).find('video');
        if ($video.length) {
            $video[0].pause();
            $video.off('ended');
        }
    }

    $(".hero__total").text(heroSlider.slick("getSlick").slideCount);

    heroSlider.on("afterChange", function(event, slick, currentSlide) {
        $(".hero__current").text(currentSlide + 1);
    });

});