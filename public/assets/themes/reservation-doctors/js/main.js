"use strict";

$(document).ajaxStart(function () {
});

$(document).ajaxComplete(function () {
    initThemeElements();
});

$(document).ready(function () {
    initThemeElements();
});


function toggleWishListDoctor(response) {

    let $wishlist_item = $('*[data-wishlist_doctor_hashed_id="' + response.hashed_id + '"]');

    if (response.action == "add") {
        $wishlist_item.addClass('btn-red');
        $wishlist_item.removeClass('btn-white');

    } else {
        $wishlist_item.addClass('btn-white');
        $wishlist_item.removeClass('btn-red');
    }
}