@extends('layout', ['title' => trans('Schedules')])

@section('content')
    <div class='container'>

        <div class="card mb-3">

            @include('breadcrumb', [
                'links' => [
                    ['name' => trans('settings.settings'), 'url' => route('settings.index')],
                    ['name' => trans('settings.scheduler'), 'url' => route('settings.show', ['setting' => 'scheduler'])]
                ],
                'page_title' => trans('settings.scheduler'),
                'stats' => [['label' => trans('user.all'), 'value' => $scheduled_tasks->count()]],
                'buttons' => [
                    [
                        'label' => trans('Schedule task'),
                        'class' => 'btn-warning',
                        'data' => 'data-bs-toggle=modal data-bs-target=.new_task',
                    ],
                ],
                'buttons_right' => [
                    [
                        'label' => "Scheduler: ".$scheduler->value,
                        'class' => 'badge '.optional($scheduler->updated_at)->diffInMinutes(now()) > 5 ? 'text-bg-danger': 'text-bg-success',
                    ],
                ],
            ])

            <div class="card-body">

                <table class='table table-hover'>
                    <thead>
                        <tr class="bg-dark text-white">
                            <th>{{ trans('schedules.th_id') }}</th>
                            <th>{{ trans('schedules.th_name') }}</th>
                            <th>{{ trans('schedules.th_command') }}</th>
                            <th>{{ trans('schedules.th_arguments') }}</th>
                            <th>{{ trans('schedules.th_frequency') }}</th>
                            <th>{{ trans('schedules.th_ping_before') }}</th>
                            <th>{{ trans('schedules.th_ping_after') }}</th>
                            <th>{{ trans('Active') }}</th>
                            <th class='text-center'>{{ trans('schedules.th_action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($scheduled_tasks as $task)
                            <tr>
                                <td>{{ $task->id }}</td>
                                <td>{{ $task->name }}</td>
                                <td>{{ $task->command }}</td>
                                <td>{{ $task->arguments }}</td>
                                <td>{{ $task->frequency }}</td>
                                <td>{{ $task->ping_before }}</td>
                                <td>{{ $task->ping_after }}</td>
                                <td>
                                    <div class='form-check form-switch'>
                                        <input type="hidden" name="active" value="0">
                                        <input class="form-check-input" type="checkbox" name="active" value="1"
                                            role="switch" id="flexSwitchCheckDefault"
                                            {{ $task->active==1? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class='text-center'>
                                    <div class="dropdown">
                                        <div data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                                            <i class="fa-solid fa-ellipsis-vertical text-dark fs-5"></i>
                                        </div>
                                        <ul class="dropdown-menu text-dark">
                                            <li>
                                                <a data-bs-toggle='modal' data-bs-target=#delete_<?= $task->id ?>
                                                    class="dropdown-item text-danger text-decoration-none"
                                                    style="cursor: pointer;">
                                                    <i class="fa fa-trash me-2" aria-hidden="true"></i>
                                                    {{ trans('actions.delete') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @foreach ($scheduled_tasks as $task)
                    @include('confirm_delete', [
                        'route' => route('schedule.destroy', ['schedule' => $task]),
                        'id' => 'delete_' . $task->id,
                        'header' => trans('actions.are_you_sure'),
                        'name' => $task->name,
                        'content_type' => 'task',
                        'delete_text' => trans('actions.delete'),
                        'cancel' => trans('actions.cancel'),
                    ])
                @endforeach

                <div class='modal new_task' id='create_file' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'
                    aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header modal-header-primary bg-primary'>
                                <h4 class='modal-title text-white'>Schedule task</h4>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <form action="{{ route('schedule.store') }}" method='POST'>
                                <div class='modal-body'>
                                    @csrf

                                    <div class='form-group'>
                                        <label for='name'>Name</label>
                                        <input type='text' class='form-control' id='name' name='name' required>
                                    </div>

                                    <div class='form-group'>
                                        <label for='frequency'>Cron</label>
                                        <input type='text' class='form-control' id='cron' name='frequency' required>
                                    </div>

                                    <div class='form-group'>
                                        <label for='command'>Command</label>
                                        <select name='command' class='form-select' required>
                                            @foreach ($commands as $key => $command)
                                                <option value='{{ $key }}'>{{ $key }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class='form-group'>
                                        <label for='ping_before'>Arguments</label>
                                        <input type='text' class='form-control' id='arguments' name='arguments'>
                                    </div>

                                    <div class='form-group'>
                                        <label for='ping_before'>Ping before</label>
                                        <input type='text' class='form-control' id='ping_before' name='ping_before'>
                                    </div>

                                    <div class='form-group'>
                                        <label for='ping_after'>Ping after</label>
                                        <input type='text' class='form-control' id='ping_after' name='ping_after'>
                                    </div>


                                </div>
                                <div class='modal-footer'>
                                    <button type='submit' class='btn btn-primary'>Schedule</button>
                                    <button type='button' class='btn btn-default' data-bs-dismiss='modal'>{{ trans('actions.cancel') }}</button>
                                </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->


            </div>
        </div>
    </div>
@endsection
