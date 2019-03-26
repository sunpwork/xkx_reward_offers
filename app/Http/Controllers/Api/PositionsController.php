<?php

namespace App\Http\Controllers\Api;

use App\Models\Position;
use App\Transformers\PositionTransformer;
use Illuminate\Http\Request;

class PositionsController extends Controller
{
    public function index(Request $request, Position $position)
    {
        $query = $position->query();

        $query->recent();
        $query->where('display', 1);
        if ($categoryId = $request->category_id) {
            $query->where('category_id', $categoryId);
        }

        $position = $query->paginate(20);
        return $this->response->paginator($position, new PositionTransformer());
    }

    public function show(Position $position)
    {
        return $this->response->item($position, new PositionTransformer());
    }

    public function myApplyIndex()
    {
        $applyRecords = $this->user->applyRecords;
        $positionIds = $applyRecords->pluck('position_id');
        $positions = Position::whereIn('id', $positionIds)->paginate(20);
        return $this->response->paginator($positions, new PositionTransformer());
    }
}
