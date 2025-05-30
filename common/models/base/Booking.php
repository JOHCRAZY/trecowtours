<?php


namespace common\models\base;

use Yii;
use yii\helpers\ArrayHelper;
use RuntimeException;
/**
 * This is the base-model class for table "bookings".
 *
 * @property integer $booking_id
 * @property integer $tour_id
 * @property integer $user_id
 * @property integer $customer_id
 * @property string $booking_type
 * @property string $booking_date
 * @property string $payment_status
 * @property string $total_amount
 * @property string $discount_applied
 * @property integer $is_deleted
 * @property integer $number_of_participants
 * @property string $booking_status
 * @property string $booking_notes
 * @property string $booking_code
 * @property string $created_at
 * @property string $updated_at
 * 
 *
 * @property \common\models\Customer $customer
 * @property \common\models\Tour $tour
 */
abstract class Booking extends \yii\db\ActiveRecord
{

    const BOOKING_STATUS_PENDING = 'pending';
    const BOOKING_STATUS_CONFIRMED = 'confirmed';
    const BOOKING_STATUS_CANCELLED = 'cancelled';
    const BOOKING_STATUS_COMPLETED = 'completed';

    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_CANCELLED = 'cancelled';

    const BOOKING_TYPE_SINGLE = 'single';
    const BOOKING_TYPE_COUPLE = 'couple';

    public $first_name;
    public $last_name;
    public $email;
    public  $phone;
    

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bookings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $parentRules = parent::rules();
        return ArrayHelper::merge($parentRules, [
            [['payment_status'], 'default', 'value' => self::PAYMENT_STATUS_PENDING],
            [['total_amount'],'default','value'=> 0.00],
            [['booking_type'], 'default', 'value' => self::BOOKING_TYPE_SINGLE],
            [['booking_status'], 'default', 'value' => self::BOOKING_STATUS_PENDING],
            [['booking_date'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['number_of_participants'], 'default', 'value' => 1],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['updated_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['booking_code'], 'default', 'value' => self::generateBookingCode(10, 'BK-', function($code)  {
                     // Check if code exists in database
                    return self::find()->where(['booking_code' => $code])->count() > 0;
                })],
            [['discount_applied'], 'default', 'value' => 0.00],
            [['is_deleted'], 'default', 'value' => 0],
            [['tour_id', 'customer_id','first_name','last_name','phone','email','number_of_participants','booking_type'], 'required','message'=> '** Required Field **'],
            [['booking_id', 'tour_id', 'user_id', 'customer_id', 'is_deleted'], 'integer'],
            [['booking_type', 'payment_status', 'booking_status'], 'string'],
            [['tour_id', 'customer_id', 'is_deleted'], 'integer'],
            [['booking_date'], 'safe'],
            [['booking_notes'],'string','max'=> 255],
            [['booking_code'], 'string', 'max' => 50],
            [['booking_status'], 'string', 'max' => 50],
            [['number_of_participants'], 'integer'],
            [['total_amount', 'discount_applied'], 'number'],
            [['booking_type', 'payment_status'], 'string', 'max' => 20],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\Customer::class, 'targetAttribute' => ['customer_id' => 'customer_id']],
            [['tour_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\Tour::class, 'targetAttribute' => ['tour_id' => 'tour_id']]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'booking_id' => Yii::t('app', 'Booking ID'),
            'tour_id' => Yii::t('app', 'Tour'),
            'customer_id' => Yii::t('app', 'Customer'),
            'booking_type' => Yii::t('app', 'Booking Type'),
            'booking_date' => Yii::t('app', 'Booking Date'),
            'payment_status' => Yii::t('app', 'Payment Status'),
            'total_amount' => Yii::t('app', 'Total Amount'),
            'discount_applied' => Yii::t('app', 'Discount Applied'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(\common\models\Customer::class, ['customer_id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTour()
    {
        return $this->hasOne(\common\models\Tour::class, ['tour_id' => 'tour_id']);
    }

    /**
 * Generates a unique, secure, and user-friendly booking code
 * 
 * @param int $length Length of the booking code (default: 8)
 * @param string $prefix Optional prefix for the booking code
 * @param callable|null $existsCallback Optional callback to check if code already exists
 * @param int $maxAttempts Maximum attempts to generate a unique code (default: 10)
 * @return string Unique booking code
 * @throws RuntimeException If unable to generate a unique code after max attempts
 */
function generateBookingCode(int $length = 8, string $prefix = '', ?callable $existsCallback = null, int $maxAttempts = 10): string
{
    // Characters to use (excluding ambiguous characters like 0/O, 1/I/l)
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $attempts = 0;
    
    do {
        // Increment attempt counter
        $attempts++;
        
        // Start with prefix if provided
        $bookingCode = $prefix;
        
        // Add high-entropy component (timestamp + random)
        $timestamp = microtime(true);
        $randVal = random_int(1000, 9999);
        $entropy = hash('xxh3', $timestamp . $randVal, true);
        
        // Add cryptographically secure random bytes
        $randomBytes = random_bytes(16);
        
        // Combine entropy sources and hash
        $combined = hash('sha256', $entropy . $randomBytes, true);
        
        // Convert hash to our character set to avoid ambiguous characters
        $remainingLength = $length - strlen($prefix);
        for ($i = 0; $i < $remainingLength; $i++) {
            $index = ord($combined[$i % strlen($combined)]) % strlen($chars);
            $bookingCode .= $chars[$index];
        }
        
        // Add hyphen for readability if code is longer than 6 characters
        if (strlen($bookingCode) > 6 && strpos($bookingCode, '-') === false) {
            $midPoint = ceil(strlen($bookingCode) / 2);
            $bookingCode = substr($bookingCode, 0, $midPoint) . '-' . substr($bookingCode, $midPoint);
        }
        
        // Check if code already exists using callback (if provided)
        $codeExists = $existsCallback !== null ? $existsCallback($bookingCode) : false;
        
    } while ($codeExists && $attempts < $maxAttempts);
    
    // Throw exception if we couldn't generate a unique code after max attempts
    if ($codeExists) {
        throw new RuntimeException("Failed to generate a unique booking code after {$maxAttempts} attempts");
    }
    
    return $bookingCode;
}

}
