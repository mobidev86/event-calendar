<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon; 
class FullCalenderController extends Controller
{
    /**
     * Show the calendar view.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Event::whereDate('start', '>=', $request->start)
                       ->whereDate('end', '<=', $request->end)
                       ->get(['id', 'title', 'start', 'end']);

            return response()->json($data);
        }

        return view('fullcalendar');
    }
 
    /**
     * Handle AJAX requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajax(Request $request)
    {
        switch ($request->type) {
            case 'add':
                $event = Event::create([
                    'title' => $request->title,
                    'start' => Carbon::parse($request->start),
                    'end' => Carbon::parse($request->end),
                ]);

                return response()->json($event);
                break;

            case 'update':
                $event = Event::find($request->id);
                if ($event) {
                    $event->update([
                        'title' => $request->title,
                        'start' => Carbon::parse($request->start),
                        'end' => Carbon::parse($request->end),
                    ]);
                }

                return response($event);
                break;

            case 'delete':
                $event = Event::find($request->id);
                if ($event) {
                    $event->delete();
                }

                return response($event);
                break;

                default:
                return response(['message' => 'Invalid request type'], 422);            
        }
    }
}