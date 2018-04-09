(function ($) {
    "use strict";

    $(document).ready(function () {
        $(".stm-mc-header-icon-wrap").on("click", function () {
            console.log($(".stm-open"));
            if($(".stm-open").length == 0) {
                $(".stm_compare_cars_footer_modal").addClass("stm-open");
            } else {
                $(".stm_compare_cars_footer_modal").removeClass("stm-open");
            }
        });

        $(document).on('click', '.add-to-compare-modal', function (e) {

            e.preventDefault();
            var stm_cookies = $.cookie();
            var stm_car_compare = [];
            var stm_car_add_to = $(this).data('id');

            for (var key in stm_cookies) {
                if (stm_cookies.hasOwnProperty(key)) {
                    if (key.indexOf('compare_ids') > -1) {
                        stm_car_compare.push(stm_cookies[key]);
                    }
                }
            }

            var stm_compare_cars_counter = stm_car_compare.length;
            $.cookie.raw = true;

            $.removeCookie('compare_ids[' + stm_car_add_to + ']', {path: '/'});
            $(this).removeClass('active');

            if($(".add-to-compare").length) {
                $(".add-to-compare").removeClass('active');
                if (typeof(stm_label_add) != 'undefined') {
                    $(".add-to-compare").text(stm_label_add);
                }
            }

            stm_compare_cars_counter--;
            var compareModal = $(".stm_compare_cars_footer_modal");

            $.ajax({
                url: ajaxurl,
                type: "POST",
                dataType: 'json',
                context: this,
                data: 'action=stm_ajax_get_compare_list',
                success: function (data) {
                    //console.log(data);
                    if(data == "empty") {
                        compareModal.hide();
                    } else {
                        $(".stm-compare-badge").show().text(stm_compare_cars_counter);
                        $(".stm-compare-list-wrap").empty();
                        $(".stm-compare-list-wrap").append(data);
                    }
                },
                error: function (er) {
                    console.log("qwe" + er);
                }
            });

            if ($('.stm_remove_after').length) {
                window.location.reload();
            }
        });
    });

})(jQuery);