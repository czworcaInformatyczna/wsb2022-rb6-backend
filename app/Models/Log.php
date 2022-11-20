<?php

namespace App\Models;

use App\Enums\LogActionType;
use App\Enums\LogItemType;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Undocumented class
 *
 * @property integer $user_id
 * @property LogActionType $action_type
 */
class Log extends Model
{
    use HasFactory;

    protected $casts = [
        'action_type' => LogActionType::class,
        'description' => 'array'
    ];

    public function item()
    {
        return $this->morphTo();
    }

    public function target()
    {
        return $this->morphTo();
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
