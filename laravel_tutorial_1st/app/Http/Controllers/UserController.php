<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule; // Import the Model

class UserController extends Controller
{
    // 1. Show the form view
    public function showScheduleForm()
    {
        return view('schedule'); // to return in the schedule.blade.php
    }

    // 2. Handle the form submission and save to DB
    public function saveSchedule(Request $request)
    {
        // Create a new Schedule entry
        Schedule::create([
            'subjcode'  => $request->input('subjcode'), // this will match the 'subjcode' field in the DB
            'day_time'  => $request->input('day_time'), // this will match the 'day_time' field in the DB
            'professor' => $request->input('professor'), // this will match the 'professor' field in the DB
        ]);

        // Redirect back to the form or a success page
        return redirect()->back()->with('success', 'Schedule Saved!');
    }
}