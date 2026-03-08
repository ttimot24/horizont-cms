<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use App\Model\ScheduledTask;
use App\Model\Settings;

class ScheduleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('settings.schedules', [
            'commands' => rescue(fn() => \Artisan::all(), fn() => []),
            'scheduled_tasks' => \App\Model\ScheduledTask::all(),
            'scheduler' => Settings::firstOrNew(['setting' => 'scheduler'], ['value' => 'not configured'])
        ]);
    }

    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $task = new ScheduledTask($request->all());
        $task->author()->associate($request->user());
        $task->active = 1;

        if ($task->save()) {
            return redirect()->back()->withMessage(['succes' => trans('Succesfully scheduled a task!')]);
        } else {
            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
        }
    }

    public function edit()
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        return null;
    }

    /**
     * Remove the specified resource from database.
     *
     * @param  \App\Model\ScheduledTask  $scheduledtask
     * @return \Illuminate\Http\Response
     */
    public function destroy(ScheduledTask $schedule)
    {


        if ($schedule->delete()) {
            return redirect()->back()->withMessage(['success' => trans('Successfully deleted the task!')]);
        }

        return redirect(route("settings.show", ['setting' => 'schedules']))->withMessage(['danger' => trans('message.something_went_wrong')]);
    }
}
