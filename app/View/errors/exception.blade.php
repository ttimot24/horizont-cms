<html>

<head>
    <title>Exception - HorizontCMS</title>
    @foreach (config('horizontcms.css') as $each_css)
        <link rel="stylesheet" type="text/css" href="{{ asset($each_css) }}">
    @endforeach
</head>

<body>

    <div style='background-color:#e0b50b' class="py-5 mb-5">
        <h1 class='container text-dark hero-title my-5' >
            <i class="fab fa-envira text-success" aria-hidden="true"></i> <b>{{ config('app.name') }} Exception</b>
        </h1>
    </div>
    <div class='container p-4'>
        <h2>
            <i class="fa fa-exclamation-circle text-danger" aria-hidden="true"></i> There was an error!
        </h2>
        <p class="p-4 fs-5">
            <b>Type:</b> {{ get_class($exception) }}
            <br><br>
            {!! $exception->getMessage() !!} on line: {{ $exception->getLine() }}<br>in: {{ $exception->getFile() }}
            <br>
        <div class='card card-body p-4'>
            <h2>Backtrace:</h2> <br>
            <div class="fs-5">
                <?php
                $counter = count($exception->getTrace()) - 1;
                foreach ($exception->getTrace() as $trace) {
                    echo '#' . $counter . ' <b>Function: </b>' . $trace['function'] . ' <br>';
                    if (isset($trace['file'])) {
                        echo '<b>File: </b>' . $trace['file'] . ' <br>';
                    }
                    if (isset($trace['line'])) {
                        echo '<b>Line: </b>' . $trace['line'] . '<br><hr><br>';
                    }
                    $counter--;
                }
                ?>
            </div>
        </div>
        </p>
    </div>


</body>

</html>
