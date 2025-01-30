<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // index
    public function index($request)
    {
        // event by category
        $events = Event::where('event_category_id', $request->event_category_id)->get();

        if ($request->category_id == 'all') {
            $events = Event::all();
        }

        // $events = Event::all();
        $events->load('eventCategory', 'Vendor');
        return response()->json([
            'status' => 'success',
            'message' => 'Event list',
            'data' => $events
        ]);
    }

    // get all events categories
    public function categories()
    {
        $categories = EventCategory::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Event categories',
            'data' => $categories
        ]);
    }

    // detail event and sku by event_id
    public function detail($request)
    {
        $event = Event::find($request->event_id);
        $event->load('eventCategory', 'vendor');
        $sku = $event->sku;
        $event['sku'] = $sku;
        return response()->json([
            'status' => 'success',
            'message' => 'Event detail',
            'data' => $event
        ]);
    }
}
