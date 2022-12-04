<?php

namespace App\Models;

use App\Enums\LogActionType;
use App\Enums\LogItemType;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

/**
 * Class responspible for logging actions.
 * For example: $user_id gave asset $item_id to user $target_id
 *
 * @property integer $id
 * @property datetime $created_at
 * @property datetime $updated_at
 * @property integer|null $user_id
 * @property string|null $item_type "Main actor type"
 * @property integer|null $item_id "Main actor ID"
 * @property string|null $target_type "Secondary actor type"
 * @property integer|null $target_id "Secondary actor ID"
 * @property LogActionType $action_type
 * @property string|null $description Should be in JSON format
 */
class Log extends Model
{
    use HasFactory;

    /**
     * Laravel casts
     *
     * @var array
     */
    protected $casts = [
        'action_type' => LogActionType::class,
        'description' => 'array',
        'item_type' => LogItemType::class,
        'target_type' => LogItemType::class
    ];

    /**
     * Polymorphic relationship that contains info about main actor
     *
     * @return MorphTo
     */
    public function item()
    {
        return $this->morphTo();
    }

    /**
     * Polymorphic relationship that contains info about secondary actor
     *
     * @return MorphTo
     */
    public function target()
    {
        return $this->morphTo();
    }

    /**
     * Returns a user that performed given action
     *
     * @return User|null
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Create new log entry
     *
     * @param LogActionType $actionType
     * @param string|null $description in JSON format
     * @param LogItemType $itemType
     * @param integer $itemId
     * @param LogItemType|null $targetType
     * @param integer|null $targetId
     * @return void
     */
    public static function newEntry(
        $actionType,
        $description,
        $itemType,
        $itemId,
        $targetType = null,
        $targetId = null,
    ) {
        $log = new Log();

        $log->user_id = Auth::user()->id;
        $log->action_type = $actionType;
        $log->description = $description;

        // Check if given entity exists
        $item = (new ($itemType->getCorrectEntity()))->withOnly([])->find($itemId);
        if (!$item) {
            throw new Exception("Item not found");
        }

        $log->item_type = $itemType;
        $log->item_id = $item->id;

        if ($targetType != null && $targetId != null) {
            // Check if given entity exists
            $target = (new ($targetType->getCorrectEntity()))->withOnly([])->find($targetId);
            if (!$target) {
                throw new Exception("Target not found");
            }

            $log->target_id = $target->id;
            $log->target_type = $targetType;
        }

        $log->save();
    }
}
