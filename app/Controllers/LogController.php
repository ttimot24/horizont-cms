<?php

namespace App\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Opcodes\LogViewer\Facades\LogViewer;
use Opcodes\LogViewer\Logs\Log;

class LogController extends Controller
{
    private const ENTRIES_PER_PAGE = 250;

    public function index($log = null): View|JsonResponse
    {
        $allFiles = LogViewer::getFiles()->sortByLatestFirst();
        $currentFile = $log
            ? LogViewer::getFile($log) ?? $allFiles->first()
            : $allFiles->first();

        if (! $currentFile) {
            $entries = new LengthAwarePaginator([], 0, self::ENTRIES_PER_PAGE);
        } else {
            $entries = $currentFile->logs()
                ->reverse()
                ->paginate(self::ENTRIES_PER_PAGE)
                ->through(function (Log $entry): object {
                    $context = '';
                    $exception = $entry->context['exception'] ?? null;

                    if (is_string($exception) && $exception !== '' && ! str_contains($context, $exception)) {
                        $context .= ($context === '' ? '' : "\n").$exception;
                    }

                    return (object) [
                        'id' => $entry->index ?? md5($context),
                        'level' => strtolower($entry->level ?? 'info'),
                        'date' => $entry->datetime ?? Carbon::now(),
                        'context' => $context,
                    ];
                });
        }

        if (request()->wantsJson()) {
            return response()->json($entries);
        }

        return view('settings.log', [
            'all_files' => $allFiles,
            'entries' => $entries,
            'current_file' => $currentFile?->name,
            'max_files' => 15,
        ]);
    }

    public function show($log): View|JsonResponse
    {
        return $this->index($log);
    }
}
