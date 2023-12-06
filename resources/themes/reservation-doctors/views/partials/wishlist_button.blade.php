<a href="{{url("wishlist/$doctor->hashed_id")}}"
   data-action="post"
   data-page_action="toggleWishListDoctor"
   data-wishlist_doctor_hashed_id="{{$doctor->hashed_id}}"
   data-style="zoom-in"
   class="btn {{\Facades\Corals\Modules\Reservation\Classes\DoctorReservations::isDoctorInWishlist($doctor) ? 'btn-red' :'btn-white'}} fav-btn">
    <i class="far fa-bookmark"></i>
</a>
