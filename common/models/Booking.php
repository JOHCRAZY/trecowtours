<?php

namespace common\models;

use \common\models\base\Booking as BaseBooking;

/**
 * This is the model class for table "bookings".
 */
class Booking extends BaseBooking
{

    public function getBookingTypes(){
        return [
            self::BOOKING_TYPE_SINGLE => 'Single',
            self::BOOKING_TYPE_COUPLE => 'Couple',
        ];
    }
}
