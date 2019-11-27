import $ from 'jquery';
import './../css/normalize.css'
import './../scss/product.scss'
import './../scss/header.scss';
import './../scss/section.scss';
import './../scss/footer.scss';



$(document).ready(() => {
    $('#main').load('./home.html')

    let _link = 'home'

    const navList = $('header ul.nav li')
    const navLine = $('header .navDiv .line')

    $('ul.nav li').on('click', function () {
        const _self = $(this)
        const link = _self.data('link')
        if (_link !== link) {
            _link = link
            $('#main').load(`./${link}.html`);
            const _index = _self.index()
            $(navList[_index]).addClass('isSelected').siblings().removeClass('isSelected')
            navLine.stop(true, true).animate({
                left: `${_index*80}px`
            })
        }
    });

    // home
    (function () {

        var lineArr = [],
            numberArr = [],
            homeImgIndex = 1

        $('#main').on('click', '#home .carousel .left', function () {
            if (homeImgIndex > 1) {

                homeImgsUl = $('#home .carousel .center ul')
                imgsWidth = $('#home .carousel .center ul img').width()
                numberArr = $('#home .three .count .number')
                lineArr = $('#home .three .count .line')

                homeImgsUl.stop(true, true).animate({
                    left: `+=${imgsWidth}`
                })

                homeImgIndex -= 1
                lineColor()
                numberEach()

                if (homeImgIndex === 1) {
                    $(this).addClass('prohibit')
                } else {
                    $('#main #home .carousel .right').removeClass('prohibit')
                }
            }
        })

        $('#main').on('click', '#home .carousel .right', function () {

            if (homeImgIndex < 3) {

                homeImgsUl = $('#home .carousel .center ul')
                imgsWidth = $('#home .carousel .center ul img').width()
                numberArr = $('#home .three .count .number')
                lineArr = $('#home .three .count .line')

                homeImgsUl.stop(true, true).animate({
                    left: `-=${imgsWidth}`
                })

                homeImgIndex += 1
                lineColor()
                numberEach()

                if (homeImgIndex === 3) {
                    $(this).addClass('prohibit')
                } else {
                    $('#main #home .carousel .left').removeClass('prohibit')
                }
            }
        })


        function numberEach() {
            numberArr.each(function (index, item) {
                if (index < homeImgIndex) {
                    $(item).addClass('isSelected')
                } else {
                    $(item).removeClass('isSelected')
                }
            })
        }

        function lineColor() {
            lineArr.each(function (index, item) {
                if (index < homeImgIndex - 1) {
                    $(item).addClass('isSelected')
                } else {
                    $(item).removeClass('isSelected')
                }
            })
        }

        $('#main').on('click', '#home .top .advisory', function () {
            const height = $('#home .two').height() +
                $('#home .three').height() +
                $('#home .four').height() +
                $('#home .fives').height() - 80

            $('body,html').animate({
                scrollTop: height
            }, 300)
        })

    }());
    // help
    (function () {

        $('#main').on('click', '#help .content .linkageTitle li', function () {
            const _self = $(this)
            const _index = _self.index()
            const linkageContent = $('#help .content .linkageContent .linkageList')

            _self.addClass('isSelected').siblings().removeClass('isSelected')
            $(linkageContent[_index]).addClass('isSelected').siblings().removeClass('isSelected')
        })

        $('#main').on('click', '.collapse .collapseTitle', function () {
            const _self = $(this)

            const _parent = _self.parent()
            const _next = _self.next()
            _next.show()

            const nextHeight = _next.outerHeight(true)
            const selfHeight = _self.outerHeight(true)

            _parent.animate({
                height: `${Math.ceil(nextHeight + selfHeight)}px`
            }, function () {
                _parent.addClass('isSelected')
            }).siblings().animate({
                height: `${$(this).outerHeight(true)}px`
            }, function () {
                $(this).removeClass('isSelected').children('*.collapseContent').hide()
            })
        })

    }())

})
