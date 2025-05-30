<?php

namespace common\models;

use \common\models\base\BookingPayments as BaseBookingPayments;

/**
 * This is the model class for table "booking_payments".
 */
class BookingPayments extends BaseBookingPayments
{

    public function init()
    {
        parent::init();
        // Set default payment date to today if not provided
        if (empty($this->payment_date)) {
            $this->payment_date = date('Y-m-d');
        }
    }
    
    /**
     * Returns list of available payment methods
     * @return array
     */
    public static function getPaymentMethods()
    {
        return [
            'cash' => 'Cash',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'bank_transfer' => 'Bank Transfer',
            'online_payment' => 'Online Payment',
            'check' => 'Check',
        ];
    }
}
