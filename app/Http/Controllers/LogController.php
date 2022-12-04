<?php

namespace App\Http\Controllers;

use App\Enums\LogItemType;
use App\Models\Log;
use App\Http\Requests\StoreLogRequest;
use App\Http\Requests\UpdateLogRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Nette\NotImplementedException;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => 'integer|nullable|min:2|max:30',
            'sort' => [
                'string',
                Rule::in([
                    'id',
                    'user_id',
                    'item_type',
                    'item_id',
                    'target_type',
                    'target_id',
                    'action_type',
                    'created_at',
                    'updated_at'
                ])
            ],
            'order' => [
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'item_type' => [
                'string',
                new Enum(LogItemType::class)
            ],
            'item_id' => [
                'integer',
                'exclude_without:item_type'
            ]
        ]);

        $log = Log::withOnly(['user', 'item', 'target']);

        if (($validated['sort'] ?? null) !== null) {
            $log = $log->orderBy($validated['sort'], ($validated['order'] ?? 'asc'));
        }

        if (($validated['item_type'] ?? null) !== null) {
            $log = $log->where('item_type', $validated['item_type']);
        }

        if (($validated['item_id'] ?? null) !== null) {
            $log = $log->where('item_id', $validated['item_id']);
        }

        return $log->paginate($validated['per_page'] ?? 10);
    }
}
