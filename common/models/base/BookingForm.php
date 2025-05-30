<?php
namespace common\models\base;

use common\models\Booking;
use common\models\Customer;
use common\models\Tour;
use yii\base\Model;
use Yii;

class BookingForm extends Model
{
    // Step 1
    public $tour_id;
    public $selected_tour_data;

    // Step 2
    public $booking_type;
    public $number_of_participants;
    public $booking_date;
    public $booking_notes;
    public $total_amount;
    
    // Step 3
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    
    private $_step = 1;
    private $_totalSteps = 3;

    public function rules()
    {
        return [
            // Step 1
            [['tour_id'], 'required', 'on' => 'step1'],
            [['tour_id'], 'validateTourAvailability', 'on' => 'step1'],
            
            // Step 2
            [['booking_type', 'number_of_participants', 'booking_date'], 'required', 'on' => 'step2'],
            ['booking_type', 'in', 'range' => [
                Booking::BOOKING_TYPE_SINGLE,
                Booking::BOOKING_TYPE_COUPLE,
                Booking::BOOKING_TYPE_FAMILY,
                Booking::BOOKING_TYPE_GROUP
            ]],
            ['number_of_participants', 'integer', 'min' => 1],
            ['booking_date', 'validateBookingDate'],
            ['booking_notes', 'string', 'max' => 255],
            ['total_amount', 'number'],
            
            // Step 3
            [['first_name', 'last_name', 'email', 'phone'], 'required', 'on' => 'step3'],
            [['first_name', 'last_name'], 'string', 'max' => 50],
            ['email', 'email'],
            ['phone', 'string', 'max' => 20],
        ];
    }

    public function validateTourAvailability($attribute, $params)
    {
        $tour = Tour::findOne($this->tour_id);
        if (!$tour) {
            $this->addError($attribute, 'Invalid tour selected.');
            return;
        }

        // Store tour data for later use
        $this->selected_tour_data = $tour;

        // Check if tour is available on selected date
        if ($tour->booking_deadline < date('Y-m-d')) {
            $this->addError($attribute, 'This tour is no longer available for booking.');
        }
    }

    public function validateBookingDate($attribute, $params)
    {
        if (!$this->booking_date) {
            return;
        }

        $tour = $this->selected_tour_data ?? Tour::findOne($this->tour_id);
        if (!$tour) {
            $this->addError($attribute, 'Invalid tour selected.');
            return;
        }

        $bookingDate = strtotime($this->booking_date);
        $startDate = strtotime($tour->start_date);
        $endDate = strtotime($tour->end_date);
        $deadline = strtotime($tour->booking_deadline);

        if ($bookingDate < time()) {
            $this->addError($attribute, 'Booking date cannot be in the past.');
        } elseif ($bookingDate > $deadline) {
            $this->addError($attribute, 'Booking date is beyond the deadline.');
        } elseif ($bookingDate < $startDate || $bookingDate > $endDate) {
            $this->addError($attribute, 'Booking date must be within tour dates.');
        }
    }

    public function setStep($step)
    {
        $this->_step = $step;
        $this->setScenario('step' . $step);
    }

    public function getStep()
    {
        return $this->_step;
    }

    public function getTotalSteps()
    {
        return $this->_totalSteps;
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Create or find customer
            $customer = Customer::findOne(['email' => $this->email]) ?? new Customer();
            $customer->setAttributes([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);
            
            if (!$customer->save()) {
                throw new \Exception('Failed to save customer');
            }

            // Create booking
            $booking = new Booking();
            $booking->setAttributes([
                'tour_id' => $this->tour_id,
                'customer_id' => $customer->customer_id,
                'booking_type' => $this->booking_type,
                'booking_date' => $this->booking_date,
                'number_of_participants' => $this->number_of_participants,
                'booking_notes' => $this->booking_notes,
                'booking_status' => Booking::BOOKING_STATUS_PENDING,
                'payment_status' => Booking::PAYMENT_STATUS_PENDING,
                'total_amount' => $this->calculateTotalAmount(),
            ]);
            
            if (!$booking->save()) {
                throw new \Exception('Failed to save booking');
            }

            $transaction->commit();
            return $booking;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function calculateTotalAmount()
    {
        $tour = $this->selected_tour_data ?? Tour::findOne($this->tour_id);
        if (!$tour) {
            return 0;
        }

        $basePrice = $tour->single_price * $this->number_of_participants;
        
        // Apply discounts based on booking type
        switch ($this->booking_type) {
            case Booking::BOOKING_TYPE_COUPLE:
                return $basePrice * 0.9; // 10% discount for couples
            case Booking::BOOKING_TYPE_FAMILY:
                return $basePrice * 0.85; // 15% discount for families
            case Booking::BOOKING_TYPE_GROUP:
                return $basePrice * 0.8; // 20% discount for groups
            default:
                return $basePrice;
        }
    }
}
