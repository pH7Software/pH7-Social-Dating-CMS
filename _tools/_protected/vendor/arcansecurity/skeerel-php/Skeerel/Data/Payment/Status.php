<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data\Payment;


use Skeerel\Util\Enum;

/**
 * Class Status
 * @package Skeerel\Data\Payment
 *
 * @method static Status PENDING()
 * @method static Status REVIEWING()
 * @method static Status CANCELED()
 * @method static Status PAID()
 * @method static Status REFUNDED()
 * @method static Status REJECTED()
 * @method static Status PARTIALLY_REFUNDED()
 * @method static Status REFUND_FAILED()
 * @method static Status DISPUTED()
 * @method static Status DISPUTE_LOST()
 * @method static Status DISPUTE_REVIEW()
 */
class Status extends Enum
{
    const PENDING = 'PENDING';

    const REVIEWING = 'REVIEWING';

    const CANCELED = 'CANCELED';

    const PAID = 'PAID';

    const REFUNDED = 'REFUNDED';

    const REJECTED = 'REJECTED';

    const PARTIALLY_REFUNDED = 'PARTIALLY_REFUNDED';

    const REFUND_FAILED = 'REFUND_FAILED';

    const DISPUTED = 'DISPUTED';

    const DISPUTE_LOST = 'DISPUTE_LOST';

    const DISPUTE_REVIEW = 'DISPUTE_REVIEW';
}